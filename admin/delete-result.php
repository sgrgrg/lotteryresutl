<?php
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$message = '';
$messageType = '';
$result = null;

if (isset($_GET['id'])) {
    $result = $lotteryFunctions->getCompleteResult($_GET['id']);
    if (!$result) {
        $message = 'Result not found.';
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $id = $_POST['id'] ?? '';
    
    if (!empty($id)) {
        if ($lotteryFunctions->deleteResult($id)) {
            header('Location: index.php?message=Result deleted successfully&type=success');
            exit;
        } else {
            $message = 'Error deleting result.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Result - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .danger-button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .danger-button:hover {
            background: #c82333;
        }
        .prize-list {
            margin: 10px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Delete Result</h1>
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
                <div class="warning-box">
                    <h3>⚠️ Warning: This action cannot be undone!</h3>
                    <p>You are about to delete the result for "<strong><?php echo htmlspecialchars($result['game_name']); ?></strong>" drawn on "<strong><?php echo $result['draw_date']; ?></strong>".</p>
                    <p>This will also delete all prizes associated with this result:</p>
                    <ul class="prize-list">
                        <?php foreach ($result['prizes'] as $prize): ?>
                            <li>
                                <?php echo htmlspecialchars($prize['prize_name']); ?> - 
                                <?php echo htmlspecialchars($prize['winning_numbers']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="admin-form">
                    <h3>Result Details</h3>
                    <p><strong>Game:</strong> <?php echo htmlspecialchars($result['game_name']); ?></p>
                    <p><strong>Draw Date:</strong> <?php echo $result['draw_date']; ?></p>
                    <p><strong>Number of Prizes:</strong> <?php echo count($result['prizes']); ?></p>
                </div>

                <form method="POST" action="" onsubmit="return confirm('Are you absolutely sure you want to delete this result and all its prizes? This action cannot be undone!');">
                    <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
                    <button type="submit" name="confirm_delete" class="danger-button">Yes, Delete Result</button>
                    <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                        Cancel
                    </a>
                </form>
            <?php else: ?>
                <p>Result not found or already deleted.</p>
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                    ← Back to Dashboard
                </a>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
