<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use App\Model\Career;

require 'vendor/autoload.php';
require_once './app/include/functions.php';
require_once './app/include/DateUtility.php';
require_once './app/include/CsvUtility.php';
require_once './app/include/FileUtility.php';
require_once './app/include/Mysql.php';
require_once './app/include/Session.php';
require_once './app/include/Cache.php';
require_once './app/include/config.php';
require_once './app/model/BaseModel.php';
require_once './app/model/Career.php';

class Api
{
    private $responseData = [], $requestData = [];

    private $services = [
        'contact_us' => [
            'name' => "",
            'email' => "",
            'phone' => "",
            'message' => "",
        ],
        'call_me_back' => [
            'name' => "",
            'email' => "",
            'phone' => "",
        ],
        'career' => [
            'name' => "",            
            'email' => "",
            'phone' => "",
            'role' => "",            
            "cv" => ""
        ],
    ];

    public function __construct()
    {
        header('Content-Type: application/json');
    }

    public function response($status = 200)
    {
        http_response_code($status);
        echo json_encode($this->responseData);
        exit;
    }

    public function validateRequest($serviceName)
    {
        if (!isset($this->services[$serviceName]))
        {
            throw new Exception('Service not found');
        }

        $serviceFields = $this->services[$serviceName];

        foreach ($serviceFields as $field => $default)
        {
            if (!isset($this->requestData[$field]) || empty($this->requestData[$field]))
            {
                throw new Exception("Field '$field' is required");
            }
        }
    }

    public function index()
    {
        $json = file_get_contents('php://input');

        try
        {
            $data = json_decode($json, true);

            if (!$data)
            {
                throw new Exception('Invalid JSON input');
            }

            $this->requestData = $data;

            if (!isset($this->requestData['service_name']))
            {
                throw new Exception('Service name is required');
            }

            $this->validateRequest($this->requestData['service_name']);

            call_user_func([$this, $this->requestData['service_name']]);

            $this->response(200);
        }
        catch (Exception $e)
        {
            $this->responseData['msg'] = $e->getMessage();

            $this->response(400);
        }
    }

    private function contact_us()
    {
        $mail = new PHPMailer(true);

        // --- Server Settings for Gmail ---
        $mail->isSMTP();                                            
        $mail->Host = 'smtp.gmail.com';                     // Gmail SMTP server
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'contact.terraventures@gmail.com';                 // Your actual Gmail address
        $mail->Password = GMAIL_APP_PASSWORD;                     // Your 16-digit App Password (no spaces)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Required encryption for port 587
        $mail->Port = 587;                                    // TCP port to connect to

        // --- Recipients ---
        $mail->setFrom($this->requestData['email'], $this->requestData['name']); // Your Gmail address and name
        $mail->addAddress('contact.terraventures@gmail.com');                 // Who is receiving the email

        // --- Content ---
        $mail->isHTML(true);                                  
        $mail->Subject = 'Contact Us from ' . $this->requestData['name'];

        $body = "<p><strong>Security:</strong></p>";
        $body = "<p><strong>Phone:</strong> {$this->requestData['phone']}</p>";
        $body .= "<p><strong>Message:</strong> {$this->requestData['message']}</p>";

        $mail->Body = $body;
        $mail->send();
        
        $this->responseData['msg'] = 'Your message has been sent successfully!';
    }

    private function call_me_back()
    {
        $mail = new PHPMailer(true);

        // --- Server Settings for Gmail ---
        $mail->isSMTP();                                            
        $mail->Host = 'smtp.gmail.com';                     // Gmail SMTP server
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'contact.terraventures@gmail.com';                 // Your actual Gmail address
        $mail->Password = GMAIL_APP_PASSWORD;                     // Your 16-digit App Password (no spaces)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Required encryption for port 587
        $mail->Port = 587;                                    // TCP port to connect to

        // --- Recipients ---
        $mail->setFrom($this->requestData['email'], $this->requestData['name']); // Your Gmail address and name
        $mail->addAddress('contact.terraventures@gmail.com');                 // Who is receiving the email

        // --- Content ---
        $mail->isHTML(true);                                  
        $mail->Subject = 'Call me back from ' . $this->requestData['name'];

        $body = "<p><strong>Security:</strong></p>";
        $body = "<p><strong>Phone:</strong> {$this->requestData['phone']}</p>";

        $mail->Body = $body;
        $mail->send();
        
        $this->responseData['msg'] = 'Your message has been sent successfully!';
    }

    public function career()
    {
        unset($this->requestData['service_name']);

        $result = FileUtility::moveFile($this->requestData['cv'], "storage/files/career/");

        if (!$result) {
            throw new Exception("Failed to move CV file");
        }

        $this->requestData['cv'] = $result;
        
        $careerModel = new Career();
        $careerModel->insert($this->requestData);

        $this->responseData['msg'] = 'Details saved successfully!';
    }
}

$mysql = new Mysql(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

$api = new Api();
$api->index();