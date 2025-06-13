<?php
session_start();

require_once '../includes/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple login throttling: limit to 5 attempts per session
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    if ($_SESSION['login_attempts'] >= 5) {
        $message = 'Too many login attempts. Please try again later.';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT password_hash FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['login_attempts'] = 0; // reset on successful login
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['login_attempts']++;
                $message = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - Lottery Results</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 50px auto;">
        <h1>Admin Login</h1>
        <?php if ($message): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required style="width: 100%; padding: 8px;" />
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;" />
            </div>
            <button type="submit" style="padding: 10px 20px;">Login</button>
        </form>
    </div>
</body>
</html>
