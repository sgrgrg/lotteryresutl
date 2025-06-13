<?php
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();

// Get game ID from URL
$gameId = $_GET['game_id'] ?? null;

if (!$gameId) {
    header('Location: index.php');
    exit;
}

// Get game information
$game = $lotteryFunctions->getGame($gameId);
if (!$game) {
    header('Location: index.php');
    exit;
}

// Get results for this game
$results = $lotteryFunctions->getGameResults($gameId);

// Handle success/error messages from redirects
$message = '';
$messageType = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageType = $_GET['type'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results - <?php echo htmlspecialchars($game['name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Manage Results - <?php echo htmlspecialchars($game['name']); ?></h1>
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

            <div style="margin-bottom: 20px;">
                <a href="add-result.php?game_id=<?php echo $gameId; ?>" style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
                    Add New Result for <?php echo htmlspecialchars($game['name']); ?>
                </a>
            </div>

            <?php if (!empty($results)): ?>
                <h3>Existing Results</h3>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Draw Date</th>
                            <th>Prizes Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <?php 
                            $prizes = $lotteryFunctions->getResultPrizes($result['id']);
                            $prizeCount = count($prizes);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['draw_date']); ?></td>
                                <td><?php echo $prizeCount; ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($result['created_at'])); ?></td>
                                <td>
                                    <a href="../results.php?game_id=<?php echo $gameId; ?>&result_id=<?php echo $result['id']; ?>" 
                                       style="margin-right: 5px;">View</a>
                                    <a href="edit-result.php?id=<?php echo $result['id']; ?>" 
                                       style="margin-right: 5px; color: #007bff;">Edit</a>
                                    <a href="delete-result.php?id=<?php echo $result['id']; ?>" 
                                       style="color: #dc3545;" 
                                       onclick="return confirm('Are you sure you want to delete this result?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No results found for this game.</p>
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
