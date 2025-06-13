<?php
require_once 'config.php';

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            die();
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }

    // Initialize database tables
    public function initializeTables() {
        try {
            // Create games table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS games (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100),
                description TEXT,
                active BOOLEAN DEFAULT true
            )");

            // Create results table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS results (
                id INT PRIMARY KEY AUTO_INCREMENT,
                game_id INT,
                draw_date DATE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (game_id) REFERENCES games(id)
            )");

            // Create prizes table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS prizes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                result_id INT,
                prize_name VARCHAR(100),
                winning_numbers TEXT,
                FOREIGN KEY (result_id) REFERENCES results(id)
            )");

            return true;
        } catch(PDOException $e) {
            echo "Error creating tables: " . $e->getMessage();
            return false;
        }
    }

    // Insert sample data
    public function insertSampleData() {
        try {
            // Insert sample games
            $this->conn->exec("INSERT IGNORE INTO games (id, name, description) VALUES 
                (1, 'Daily Lotto', 'Daily lottery draw with exciting prizes'),
                (2, 'Weekly Mega', 'Weekly mega lottery with huge jackpots'),
                (3, 'Super Draw', 'Special monthly super draw')");

            // Insert sample results
            $this->conn->exec("INSERT IGNORE INTO results (id, game_id, draw_date) VALUES 
                (1, 1, '2024-01-15'),
                (2, 2, '2024-01-14'),
                (3, 1, '2024-01-13')");

            // Insert sample prizes
            $this->conn->exec("INSERT IGNORE INTO prizes (result_id, prize_name, winning_numbers) VALUES 
                (1, '1st Prize', '783456'),
                (1, '2nd Prize', '348219, 234875'),
                (1, '3rd Prize', '567890, 123456, 789012'),
                (2, '1st Prize', '987654'),
                (2, '2nd Prize', '456789, 321098'),
                (3, '1st Prize', '111222'),
                (3, '2nd Prize', '333444, 555666')");

            return true;
        } catch(PDOException $e) {
            echo "Error inserting sample data: " . $e->getMessage();
            return false;
        }
    }
}
?>
