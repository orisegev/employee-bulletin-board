<?php
namespace Tests\Services;

use PDO;
use PHPUnit\Framework\TestCase;
use App\Services\MessageService;

final class MessageServiceTest extends TestCase
{
    private \PDO $pdo;
    private MessageService $service;
    private string $testToken;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE employee_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                message TEXT NOT NULL,
                user_token TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        $this->testToken = uniqid('test_');
        $this->service = new MessageService($this->pdo);
    }

    public function testAddMessageInsertsSuccessfully(): void
    {
        $insertId = $this->service->addMessage(
            'Ori',
            'ori@example.com',
            'בדיקה של insert',
            $this->testToken
        );

        $this->assertIsInt($insertId);
        $this->assertGreaterThan(0, $insertId);
    }

    public function testAddMessageThrowsWithoutToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User token is required');

        $this->service->addMessage('Ori', 'ori@example.com', 'ללא טוקן', null);
    }

    public function testGetMessagesHidesOtherTokens(): void
    {
        $this->service->addMessage('User1', 'u1@test.com', 'msg1', 'token1');
        $this->service->addMessage('User2', 'u2@test.com', 'msg2', 'token2');

        $messages = $this->service->getMessages('token1');

        $this->assertCount(2, $messages);

        foreach ($messages as $msg) {
            if ($msg['user_token'] === 'token1') {
                $this->assertEquals('token1', $msg['user_token']);
            } else {
                $this->assertNull($msg['user_token'], 'User token should be hidden from others');
            }
        }
    }

    public function testDeleteMessageSuccess(): void
    {
        $token = 'token_to_delete';
        $id = $this->service->addMessage(
            'Ori',
            'ori@example.com',
            'Message to be deleted',
            $token
        );

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $result = $this->service->deleteMessage($id, $token);

        $this->assertTrue($result);
    }

    public function testDeleteMessageFailsOnWrongToken(): void
    {
        $correctToken = 'correct_token_' . uniqid();
        $wrongToken = 'wrong_token_' . uniqid();

        $id = $this->service->addMessage(
            'Ori',
            'ori@example.com',
            'שגיאת טוקן',
            $correctToken
        );

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Token mismatch or message not found');

        $this->service->deleteMessage($id, $wrongToken);
    }
}
