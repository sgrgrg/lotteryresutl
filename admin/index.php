<?php
require_once 'auth.php';
require_once '../includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$games = $lotteryFunctions->getAllGamesForAdmin();


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
    <title>Admin Panel - Lottery Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Lottery Results Admin Panel</h1>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="../index.php">Home</a></li>
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

            <h2>Admin Dashboard</h2>
            
            <!-- Games List -->
            <h3>Manage Games</h3>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Game Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($game['name']); ?></td>
                            <td><?php echo htmlspecialchars($game['description']); ?></td>
                            <td><?php echo $game['active'] ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <a href="manage-results.php?game_id=<?php echo $game['id']; ?>" style="margin-right: 5px;">Manage Results</a>
                                <a href="edit-game.php?id=<?php echo $game['id']; ?>" style="margin-right: 5px; color: #007bff;">Edit</a>
                                <a href="delete-game.php?id=<?php echo $game['id']; ?>" style="color: #dc3545;" onclick="return confirm('Are you sure you want to delete this game?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Recent Results -->
            <h3>Recent Results</h3>
            <?php 
            $recentResults = $lotteryFunctions->getAllResults();
            $recentResults = array_slice($recentResults, 0, 10); // Show only last 10 results
            ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Draw Date</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentResults as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['game_name']); ?></td>
                            <td><?php echo $result['draw_date']; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($result['created_at'])); ?></td>
                            <td>
                                <a href="edit-result.php?id=<?php echo $result['id']; ?>" style="margin-right: 5px; color: #007bff;">Edit</a>
                                <a href="delete-result.php?id=<?php echo $result['id']; ?>" style="color: #dc3545;" onclick="return confirm('Are you sure you want to delete this result?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                <a href="add-game.php" class="admin-form button" style="display: inline-block; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px;">
                    Add New Game
                </a>
                <a href="add-result.php" class="admin-form button" style="display: inline-block; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">
                    Add New Result
                </a>
            </div>
        </section>
    </main>
</body>
</html>
