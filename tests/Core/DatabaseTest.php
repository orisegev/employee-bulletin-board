<?php
declare(strict_types=1);

namespace Tests\Core;

use PDO;
use PHPUnit\Framework\TestCase;
use App\Core\Database;


final class DatabaseTest extends TestCase
{
    public function testConnectionReturnsPDO(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->assertInstanceOf(\PDO::class, $db);
    }

    public function testSimpleQueryReturnsData(): void
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT 1");
        $result = $stmt->fetchColumn();
        $this->assertSame(1, (int)$result);
    }
}
