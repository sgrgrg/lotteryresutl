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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $active = isset($_POST['active']) ? 1 : 0;

    if (!empty($id) && !empty($name)) {
        if ($lotteryFunctions->updateGame($id, $name, $description, $active)) {
            $message = 'Game updated successfully!';
            $messageType = 'success';
            $game = $lotteryFunctions->getGame($id); // Refresh game data
        } else {
            $message = 'Error updating game.';
            $messageType = 'error';
        }
    } else {
        $message = 'Game ID and name are required.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Edit Game</h1>
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
                <form class="admin-form" method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $game['id']; ?>">
                    
                    <div class="form-group">
                        <label for="name">Game Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($game['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($game['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="active" <?php echo $game['active'] ? 'checked' : ''; ?>> Active
                        </label>
                    </div>

                    <button type="submit">Update Game</button>
                </form>
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
