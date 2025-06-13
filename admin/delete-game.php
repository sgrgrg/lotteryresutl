<?php
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$message = '';
$messageType = '';
$game = null;

if (isset($_GET['id'])) {
    $game = $lotteryFunctions->getGame($_GET['id']);
    if (!$game) {
        $message = 'Game not found.';
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $id = $_POST['id'] ?? '';
    
    if (!empty($id)) {
        if ($lotteryFunctions->deleteGame($id)) {
            header('Location: index.php?message=Game deleted successfully&type=success');
            exit;
        } else {
            $message = 'Error deleting game.';
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
    <title>Delete Game - Admin Panel</title>
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Delete Game</h1>
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

            <?php if ($game): ?>
                <div class="warning-box">
                    <h3>⚠️ Warning: This action cannot be undone!</h3>
                    <p>You are about to delete the game "<strong><?php echo htmlspecialchars($game['name']); ?></strong>".</p>
                    <p>This will also delete:</p>
                    <ul>
                        <li>All results associated with this game</li>
                        <li>All prizes associated with those results</li>
                    </ul>
                </div>

                <div class="admin-form">
                    <h3>Game Details</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($game['name']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($game['description']); ?></p>
                    <p><strong>Status:</strong> <?php echo $game['active'] ? 'Active' : 'Inactive'; ?></p>
                </div>

                <form method="POST" action="" onsubmit="return confirm('Are you absolutely sure you want to delete this game and all its data? This action cannot be undone!');">
                    <input type="hidden" name="id" value="<?php echo $game['id']; ?>">
                    <button type="submit" name="confirm_delete" class="danger-button">Yes, Delete Game</button>
                    <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                        Cancel
                    </a>
                </form>
            <?php else: ?>
                <p>Game not found or already deleted.</p>
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                    ← Back to Dashboard
                </a>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
