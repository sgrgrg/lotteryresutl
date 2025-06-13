<?php
require_once 'includes/functions.php';

$lotteryFunctions = new LotteryFunctions();

// Get game ID from URL
$gameId = $_GET['game_id'] ?? null;
$resultId = $_GET['result_id'] ?? null;

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

// If specific result ID is provided, get that result
$selectedResult = null;
if ($resultId) {
    $selectedResult = $lotteryFunctions->getCompleteResult($resultId);
} elseif (!empty($results)) {
    // Get the latest result by default
    $selectedResult = $lotteryFunctions->getCompleteResult($results[0]['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['name']); ?> - Lottery Results</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🎲 Lottery Results Hub</h1>
            <p style="text-align: center; margin-top: 0.5rem; opacity: 0.9; font-size: 1.1rem;">Your Gateway to Winning Numbers</p>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="admin/index.php">⚙️ Admin Panel</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- Game Information -->
        <section class="games-section">
            <h2>🎯 <?php echo htmlspecialchars($game['name']); ?></h2>
            <p><?php echo htmlspecialchars($game['description']); ?></p>
        </section>

        <!-- Current Result Display -->
        <?php if ($selectedResult): ?>
            <section class="result-details">
                <div class="result-meta">
                    <h3>Draw Date: <?php echo date('M d, Y', strtotime($selectedResult['draw_date'])); ?></h3>
                    <p>Game: <?php echo htmlspecialchars($selectedResult['game_name']); ?></p>
                </div>

                <?php if (!empty($selectedResult['prizes'])): ?>
                    <h3>Winning Numbers</h3>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Prize Name</th>
                                <th>Winning Numbers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($selectedResult['prizes'] as $prize): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($prize['prize_name']); ?></td>
                                    <td><?php echo htmlspecialchars($prize['winning_numbers']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No prize information available for this draw.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- Historical Results -->
        <?php if (!empty($results)): ?>
            <section class="result-details">
                <h3>Historical Results</h3>
                <div class="table-container">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Draw Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr <?php echo ($selectedResult && $result['id'] == $selectedResult['id']) ? 'class="highlight-row"' : ''; ?>>
                                    <td><?php echo date('M d, Y', strtotime($result['draw_date'])); ?></td>
                                    <td>
                                        <a href="results.php?game_id=<?php echo $gameId; ?>&result_id=<?php echo $result['id']; ?>" class="view-details-btn" style="color:white" >
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else: ?>
            <section class="result-details">
                <p>No results available for this game yet.</p>
            </section>
        <?php endif; ?>

        <!-- Back to Games -->
        <section class="result-details">
            <a href="index.php" class="back-btn">
                ← Back to All Games
            </a>
        </section>
    </main>
</body>
</html>
