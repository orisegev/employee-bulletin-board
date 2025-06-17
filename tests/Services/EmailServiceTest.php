<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\EmailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class EmailServiceTest extends TestCase
{
    public function testSendReturnsTrueWhenMailSentSuccessfully(): void
    {
        $mockMailer = $this->createMock(PHPMailer::class);
        $mockMailer->expects($this->once())
                   ->method('send')
                   ->willReturn(true);

        $service = new EmailService($mockMailer);
        $result = $service->send('test@example.com', 'Test User', 'Test Subject', '<p>Hello</p>');

        $this->assertTrue($result);
    }

    public function testSendReturnsFalseWhenMailFails(): void
    {
        $mockMailer = $this->createMock(PHPMailer::class);
        $mockMailer->expects($this->once())
                   ->method('send')
                   ->willThrowException(new Exception('SMTP error'));

        $service = new EmailService($mockMailer);
        $result = $service->send('fail@example.com', 'Fail User', 'Subject', 'Body');

        $this->assertFalse($result);
    }
}
