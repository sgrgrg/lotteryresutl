<?php
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$message = '';
$messageType = '';
$result = null;
$games = $lotteryFunctions->getAllGames();

if (isset($_GET['id'])) {
    $result = $lotteryFunctions->getCompleteResult($_GET['id']);
    if (!$result) {
        $message = 'Result not found.';
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_result':
                $id = $_POST['id'] ?? '';
                $gameId = $_POST['game_id'] ?? '';
                $drawDate = $_POST['draw_date'] ?? '';

                if (!empty($id) && !empty($gameId) && !empty($drawDate)) {
                    if ($lotteryFunctions->updateResult($id, $gameId, $drawDate)) {
                        $message = 'Result updated successfully!';
                        $messageType = 'success';
                        $result = $lotteryFunctions->getCompleteResult($id); // Refresh result data
                    } else {
                        $message = 'Error updating result.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'update_prize':
                $prizeId = $_POST['prize_id'] ?? '';
                $prizeName = $_POST['prize_name'] ?? '';
                $winningNumbers = $_POST['winning_numbers'] ?? '';

                if (!empty($prizeId) && !empty($prizeName) && !empty($winningNumbers)) {
                    if ($lotteryFunctions->updatePrize($prizeId, $prizeName, $winningNumbers)) {
                        $message = 'Prize updated successfully!';
                        $messageType = 'success';
                        $result = $lotteryFunctions->getCompleteResult($_POST['result_id']); // Refresh result data
                    } else {
                        $message = 'Error updating prize.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'add_prize':
                $resultId = $_POST['result_id'] ?? '';
                $prizeName = $_POST['new_prize_name'] ?? '';
                $winningNumbers = $_POST['new_winning_numbers'] ?? '';

                if (!empty($resultId) && !empty($prizeName) && !empty($winningNumbers)) {
                    if ($lotteryFunctions->addPrize($resultId, $prizeName, $winningNumbers)) {
                        $message = 'Prize added successfully!';
                        $messageType = 'success';
                        $result = $lotteryFunctions->getCompleteResult($resultId); // Refresh result data
                    } else {
                        $message = 'Error adding prize.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'delete_prize':
                $prizeId = $_POST['prize_id'] ?? '';
                $resultId = $_POST['result_id'] ?? '';

                if (!empty($prizeId)) {
                    if ($lotteryFunctions->deletePrize($prizeId)) {
                        $message = 'Prize deleted successfully!';
                        $messageType = 'success';
                        $result = $lotteryFunctions->getCompleteResult($resultId); // Refresh result data
                    } else {
                        $message = 'Error deleting prize.';
                        $messageType = 'error';
                    }
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Result - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .prize-entry {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .delete-prize {
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        #add-prize-form {
            border: 2px dashed #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Edit Result</h1>
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

            <?php if ($result): ?>
                <!-- Edit Result Form -->
                <form class="admin-form" method="POST" action="">
                    <input type="hidden" name="action" value="update_result">
                    <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
                    
                    <div class="form-group">
                        <label for="game_id">Game:</label>
                        <select id="game_id" name="game_id" required>
                            <?php foreach ($games as $game): ?>
                                <option value="<?php echo $game['id']; ?>" <?php echo $game['id'] == $result['game_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($game['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="draw_date">Draw Date:</label>
                        <input type="date" id="draw_date" name="draw_date" value="<?php echo $result['draw_date']; ?>" required>
                    </div>

                    <button type="submit">Update Result</button>
                </form>

                <!-- Edit Prizes Section -->
                <h3>Prizes</h3>
                <?php foreach ($result['prizes'] as $prize): ?>
                    <div class="prize-entry">
                        <form method="POST" action="" style="display: flex; gap: 10px; align-items: flex-start;">
                            <input type="hidden" name="action" value="update_prize">
                            <input type="hidden" name="prize_id" value="<?php echo $prize['id']; ?>">
                            <input type="hidden" name="result_id" value="<?php echo $result['id']; ?>">
                            
                            <div class="form-group" style="flex: 1;">
                                <label>Prize Name:</label>
                                <input type="text" name="prize_name" value="<?php echo htmlspecialchars($prize['prize_name']); ?>" required>
                            </div>
                            
                            <div class="form-group" style="flex: 2;">
                                <label>Winning Numbers:</label>
                                <input type="text" name="winning_numbers" value="<?php echo htmlspecialchars($prize['winning_numbers']); ?>" required>
                            </div>
                            
                            <div style="display: flex; gap: 5px; margin-top: 24px;">
                                <button type="submit">Update</button>
                                <button type="submit" name="action" value="delete_prize" class="delete-prize" 
                                        onclick="return confirm('Are you sure you want to delete this prize?')">Delete</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>

                <!-- Add New Prize Form -->
                <div id="add-prize-form">
                    <h4>Add New Prize</h4>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add_prize">
                        <input type="hidden" name="result_id" value="<?php echo $result['id']; ?>">
                        
                        <div class="form-group">
                            <label>Prize Name:</label>
                            <input type="text" name="new_prize_name" placeholder="e.g., 1st Prize" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Winning Numbers:</label>
                            <input type="text" name="new_winning_numbers" placeholder="e.g., 123456, 789012" required>
                        </div>
                        
                        <button type="submit">Add Prize</button>
                    </form>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                    ← Back to Dashboard
                </a>
            </div>
        </section>
    </main>
</body>
</html>
