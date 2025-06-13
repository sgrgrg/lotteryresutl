<?php
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$games = $lotteryFunctions->getAllGames();

$message = '';
$messageType = '';
$selectedGameId = $_GET['game_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameId = $_POST['game_id'] ?? '';
    $drawDate = $_POST['draw_date'] ?? '';
    $prizes = $_POST['prizes'] ?? [];
    $prizeNames = $_POST['prize_names'] ?? [];
    $winningNumbers = $_POST['winning_numbers'] ?? [];

    if (!empty($gameId) && !empty($drawDate)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Start transaction
            $conn->beginTransaction();
            
            // Insert result
            $stmt = $conn->prepare("INSERT INTO results (game_id, draw_date) VALUES (?, ?)");
            $stmt->execute([$gameId, $drawDate]);
            $resultId = $conn->lastInsertId();
            
            // Insert prizes
            $prizeStmt = $conn->prepare("INSERT INTO prizes (result_id, prize_name, winning_numbers) VALUES (?, ?, ?)");
            
            foreach ($prizeNames as $key => $prizeName) {
                if (!empty($prizeName) && !empty($winningNumbers[$key])) {
                    $prizeStmt->execute([$resultId, $prizeName, $winningNumbers[$key]]);
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            $message = 'Result added successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = 'Game and draw date are required.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Result - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .prize-entry {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .remove-prize {
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        #add-prize {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Add New Result</h1>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="index.php">Admin Dashboard</a></li>
                <li><a href="add-game.php">Add New Game</a></li>
                <li><a href="add-result.php">Add New Result</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <section class="admin-panel">
            <?php if ($message): ?>
                <div class="<?php echo $messageType; ?>-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form class="admin-form" method="POST" action="">
                <div class="form-group">
                    <label for="game_id">Select Game:</label>
                    <select id="game_id" name="game_id" required>
                        <option value="">Select a game...</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?php echo $game['id']; ?>" <?php echo $selectedGameId == $game['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($game['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="draw_date">Draw Date:</label>
                    <input type="date" id="draw_date" name="draw_date" required>
                </div>

                <div id="prizes-container">
                    <h3>Prizes</h3>
                    <div class="prize-entry">
                        <div class="form-group">
                            <label>Prize Name:</label>
                            <input type="text" name="prize_names[]" placeholder="e.g., 1st Prize" required>
                        </div>
                        <div class="form-group">
                            <label>Winning Numbers:</label>
                            <input type="text" name="winning_numbers[]" placeholder="e.g., 123456, 789012" required>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-prize">Add Another Prize</button>

                <button type="submit">Add Result</button>
            </form>

            <div style="margin-top: 20px;">
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                    ← Back to Dashboard
                </a>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('add-prize').addEventListener('click', function() {
            const container = document.getElementById('prizes-container');
            const prizeEntry = document.createElement('div');
            prizeEntry.className = 'prize-entry';
            
            prizeEntry.innerHTML = `
                <div class="form-group">
                    <label>Prize Name:</label>
                    <input type="text" name="prize_names[]" placeholder="e.g., 1st Prize" required>
                </div>
                <div class="form-group">
                    <label>Winning Numbers:</label>
                    <input type="text" name="winning_numbers[]" placeholder="e.g., 123456, 789012" required>
                </div>
                <button type="button" class="remove-prize">Remove Prize</button>
            `;
            
            container.appendChild(prizeEntry);

            // Add remove functionality
            prizeEntry.querySelector('.remove-prize').addEventListener('click', function() {
                container.removeChild(prizeEntry);
            });
        });
    </script>
</body>
</html>
