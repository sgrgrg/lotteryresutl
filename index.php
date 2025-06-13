<?php
require_once 'includes/functions.php';

$lotteryFunctions = new LotteryFunctions();
$games = $lotteryFunctions->getAllGames();

// Handle search
$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $gameId = $_POST['game_id'] ?? null;
    
    $searchResults = $lotteryFunctions->searchResults($startDate, $endDate, $gameId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lottery Results Hub - Your Gateway to Winning Numbers</title>
    <meta name="description" content="Check the latest lottery results, search historical draws, and stay updated with winning numbers from all your favorite lottery games.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
               
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h2>Find Your Lucky Numbers</h2>
                <p>Search through thousands of lottery results and discover your winning potential</p>
            </div>
        </section>

        <!-- Search Form -->
        <section class="search-form">
            <h2>🔍 Search Results</h2>
            <form method="POST" action="" class="search-grid">
                <div class="form-group">
                    <label for="game_id">🎮 Select Game:</label>
                    <select name="game_id" id="game_id">
                        <option value="">All Games</option>
                        <?php foreach ($games as $game): ?>
                            <option value="<?php echo htmlspecialchars($game['id']); ?>" 
                                    <?php echo (isset($_POST['game_id']) && $_POST['game_id'] == $game['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($game['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">📅 Start Date:</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">📅 End Date:</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="search-btn">
                        <span>🔍 Search Results</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Games List -->
        <section class="games-section">
            <h2>🎯 Available Lottery Games</h2>
            <div class="game-list">
                <?php if (empty($games)): ?>
                    <div class="no-games">
                        <p>No lottery games available at the moment.</p>
                        <a href="admin/add-game.php" class="admin-link">Add New Game</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($games as $game): ?>
                        <div class="game-card">
                            <div class="game-icon">🎲</div>
                            <h3><?php echo htmlspecialchars($game['name']); ?></h3>
                            <p><?php echo htmlspecialchars($game['description']); ?></p>
                            <a href="results.php?game_id=<?php echo $game['id']; ?>" class="view-results-btn">
                                View Latest Results →
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Search Results -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <section class="search-results">
                <h2>📊 Search Results</h2>
                <?php if (!empty($searchResults)): ?>
                    <div class="results-count">
                        <p>Found <?php echo count($searchResults); ?> result(s)</p>
                    </div>
                    <div class="table-container">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>🎮 Game</th>
                                    <th>📅 Draw Date</th>
                                    <th>🔗 Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($result['game_name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($result['draw_date'])); ?>
                                        </td>
                                        <td>
                                            <a href="results.php?game_id=<?php echo $result['game_id']; ?>&result_id=<?php echo $result['id']; ?>" 
                                               class="view-details-btn">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <div class="no-results-icon">🔍</div>
                        <h3>No Results Found</h3>
                        <p>No lottery results match your search criteria. Try adjusting your filters or search for a different date range.</p>
                        <button onclick="document.querySelector('.search-form').scrollIntoView({behavior: 'smooth'})" 
                                class="try-again-btn">
                            Try Different Search
                        </button>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- Footer Info -->
        <section class="footer-info">
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-icon">⚡</div>
                    <h3>Real-time Updates</h3>
                    <p>Get the latest lottery results as soon as they're announced</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">📊</div>
                    <h3>Historical Data</h3>
                    <p>Access comprehensive historical lottery data for analysis</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">🔒</div>
                    <h3>Secure & Reliable</h3>
                    <p>Your trusted source for accurate lottery information</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Lottery Results Hub. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add smooth scrolling and form enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus on search form if no results are shown
            if (!document.querySelector('.search-results')) {
                const gameSelect = document.getElementById('game_id');
                if (gameSelect) gameSelect.focus();
            }

            // Add loading state to search button
            const searchForm = document.querySelector('.search-form form');
            if (searchForm) {
                searchForm.addEventListener('submit', function() {
                    const button = this.querySelector('button[type="submit"]');
                    button.innerHTML = '<span>🔍 Searching...</span>';
                    button.disabled = true;
                });
            }
        });
    </script>
</body>
</html>
