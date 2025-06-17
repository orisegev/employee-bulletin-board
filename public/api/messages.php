<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Database;
use App\Services\MessageService;
use App\Factories\MailerFactory;
use App\Services\EmailService;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

header('Content-Type: application/json');

$pdo = Database::getInstance()->getConnection();
$service = new MessageService($pdo);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $token = $_COOKIE['user_token'] ?? null;
        try {
            $messages = $service->getMessages($token);
            echo json_encode($messages);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name'], $data['email'], $data['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing data']);
            exit;
        }
        
        $name = trim(filter_var($data['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
		$message = trim(filter_var($data['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

		if (!$email) {
			http_response_code(400);
			echo json_encode(['error' => 'כתובת אימייל לא תקינה']);
			exit;
		}

        $token = $_COOKIE['user_token'] ?? bin2hex(random_bytes(16));
        if (!isset($_COOKIE['user_token'])) {
            setcookie('user_token', $token, time() + 86400 * 30, "/");
        }

        try {
            $insertId = $service->addMessage($name, $email, $message, $token);

            $mailer = MailerFactory::create();
            $emailService = new EmailService($mailer);
            
            $Subject = 'מודעתך פורסמה בהצלחה!';
            $templatePath = __DIR__ . '/../../EmailTemplates/newmessage_template.html';
            $template = file_get_contents($templatePath);
            if (!file_exists($templatePath)) {
                error_log("Template not found at $templatePath");
            }
            $template = str_replace('{{name}}', $name, $template);
            $template = str_replace('{{reference_number}}', $insertId, $template);
            $emailService->send($email, $name, $Subject, $template);

            echo json_encode(['success' => true, 'insert_id' => $insertId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['message_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing message_id']);
            exit;
        }
        $token = $_COOKIE['user_token'] ?? null;
        if (!$token) {
            http_response_code(403);
            echo json_encode(['error' => 'No user token']);
            exit;
        }
        try {
            $service->deleteMessage((int)$data['message_id'], $token);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
