<?php
require_once 'auth.php';
require_once '../includes/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $active = isset($_POST['active']) ? 1 : 0;

    if (!empty($name)) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("INSERT INTO games (name, description, active) VALUES (?, ?, ?)");
            $result = $stmt->execute([$name, $description, $active]);
            
            if ($result) {
                $message = 'Game added successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error adding game.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = 'Game name is required.';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Game - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Add New Game</h1>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="index.php">Admin Dashboard</a></li>
                <li><a href="add-game.php">Add New Game</a></li>
                <li><a href="add-result.php">Add New Result</a></li>
                <li><a href="logout.php">Logout</a></li>
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
                    <label for="name">Game Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="active" checked> Active
                    </label>
                </div>

                <button type="submit">Add Game</button>
            </form>

            <div style="margin-top: 20px;">
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 4px;">
                    ← Back to Dashboard
                </a>
            </div>
        </section>
    </main>
</body>
</html>
