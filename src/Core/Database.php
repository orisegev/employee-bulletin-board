<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    
    private ?PDO $pdo;
    private static ?self $instance = null;

    private function __construct() {
        try {
            $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
            $dbName = $_ENV['DB_NAME'] ?? '';
            $dbUser = $_ENV['DB_USER'] ?? '';
            $dbPass = $_ENV['DB_PASS'] ?? '';
            $dbPort = $_ENV['DB_PORT'] ?? '3306';
            $dbCharset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=$dbCharset";

            $this->pdo = new PDO(
                $dsn,
                $dbUser,
                $dbPass
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new PDOException("Failed to connect to the database.", 0, $e);
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}