<?php
namespace App\Services;

use PDO;
use Exception;

class MessageService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getMessages(?string $userToken = null): array {
        $isSqlite = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';

        $sql = $isSqlite
            ? "SELECT id, message, name, email, created_at AS formatted_date, user_token
            FROM employee_messages ORDER BY id DESC"
            : "SELECT id, message, name, email, DATE_FORMAT(created_at, '%d/%m/%Y - %H:%i') AS formatted_date, user_token
            FROM employee_messages ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($userToken === null || $row['user_token'] !== $userToken) {
                $row['user_token'] = null;
            }
            $messages[] = $row;
        }
        return $messages;
    }

    public function addMessage(string $name, string $email, string $message, ?string $userToken = null): int {
        if ($userToken === null) {
            throw new Exception("User token is required");
        }

        $sql = "INSERT INTO employee_messages (name, email, message, user_token) VALUES (:name, :email, :message, :user_token)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_token', $userToken);

        if ($stmt->execute()) {
            return (int)$this->pdo->lastInsertId();
        } else {
            throw new Exception("Failed to insert message");
        }
    }
    public function deleteMessage(int $messageId, string $userToken): bool {
        $sql = "SELECT id FROM employee_messages WHERE id = :message_id AND user_token = :user_token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':message_id', $messageId);
        $stmt->bindParam(':user_token', $userToken);
        $stmt->execute();

        if (!$stmt->fetch()) {
            throw new Exception("Token mismatch or message not found");
        }

        $deleteSql = "DELETE FROM employee_messages WHERE id = :message_id AND user_token = :user_token";
        $deleteStmt = $this->pdo->prepare($deleteSql);
        $deleteStmt->bindParam(':message_id', $messageId);
        $deleteStmt->bindParam(':user_token', $userToken);

        if ($deleteStmt->execute()) {
            return true;
        }

        throw new Exception("Failed to delete message");
    }

}
