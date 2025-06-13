<?php
require_once 'database.php';

class LotteryFunctions {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Get all active games
    public function getAllGames() {
        try {
            $stmt = $this->conn->query("SELECT * FROM games WHERE active = true ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get game by ID
    public function getGame($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM games WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get results for a specific game
    public function getGameResults($gameId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, g.name as game_name 
                FROM results r 
                JOIN games g ON r.game_id = g.id 
                WHERE g.id = ? 
                ORDER BY r.draw_date DESC
            ");
            $stmt->execute([$gameId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get prizes for a specific result
    public function getResultPrizes($resultId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM prizes 
                WHERE result_id = ? 
                ORDER BY id
            ");
            $stmt->execute([$resultId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get complete result details including prizes
    public function getCompleteResult($resultId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, g.name as game_name 
                FROM results r 
                JOIN games g ON r.game_id = g.id 
                WHERE r.id = ?
            ");
            $stmt->execute([$resultId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result['prizes'] = $this->getResultPrizes($resultId);
            }
            
            return $result;
        } catch(PDOException $e) {
            return false;
        }
    }

    // Search results by date range
    public function searchResults($startDate = null, $endDate = null, $gameId = null) {
        try {
            $sql = "
                SELECT r.*, g.name as game_name 
                FROM results r 
                JOIN games g ON r.game_id = g.id 
                WHERE 1=1
            ";
            $params = [];

            if ($startDate) {
                $sql .= " AND r.draw_date >= ?";
                $params[] = $startDate;
            }
            if ($endDate) {
                $sql .= " AND r.draw_date <= ?";
                $params[] = $endDate;
            }
            if ($gameId) {
                $sql .= " AND g.id = ?";
                $params[] = $gameId;
            }

            $sql .= " ORDER BY r.draw_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // UPDATE operations
    public function updateGame($id, $name, $description, $active) {
        try {
            $stmt = $this->conn->prepare("UPDATE games SET name = ?, description = ?, active = ? WHERE id = ?");
            return $stmt->execute([$name, $description, $active, $id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateResult($id, $gameId, $drawDate) {
        try {
            $stmt = $this->conn->prepare("UPDATE results SET game_id = ?, draw_date = ? WHERE id = ?");
            return $stmt->execute([$gameId, $drawDate, $id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updatePrize($id, $prizeName, $winningNumbers) {
        try {
            $stmt = $this->conn->prepare("UPDATE prizes SET prize_name = ?, winning_numbers = ? WHERE id = ?");
            return $stmt->execute([$prizeName, $winningNumbers, $id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function addPrize($resultId, $prizeName, $winningNumbers) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO prizes (result_id, prize_name, winning_numbers) VALUES (?, ?, ?)");
            return $stmt->execute([$resultId, $prizeName, $winningNumbers]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // DELETE operations
    public function deleteGame($id) {
        try {
            // First delete all related results and prizes
            $stmt = $this->conn->prepare("
                DELETE p FROM prizes p 
                INNER JOIN results r ON p.result_id = r.id 
                WHERE r.game_id = ?
            ");
            $stmt->execute([$id]);

            $stmt = $this->conn->prepare("DELETE FROM results WHERE game_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->conn->prepare("DELETE FROM games WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function deleteResult($id) {
        try {
            // First delete all related prizes
            $stmt = $this->conn->prepare("DELETE FROM prizes WHERE result_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->conn->prepare("DELETE FROM results WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function deletePrize($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM prizes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get all games (including inactive ones for admin)
    public function getAllGamesForAdmin() {
        try {
            $stmt = $this->conn->query("SELECT * FROM games ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get all results for admin
    public function getAllResults() {
        try {
            $stmt = $this->conn->query("
                SELECT r.*, g.name as game_name 
                FROM results r 
                JOIN games g ON r.game_id = g.id 
                ORDER BY r.draw_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
