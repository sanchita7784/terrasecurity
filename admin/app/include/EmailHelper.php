<?php
namespace App;

use App\Model\Setting;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use PHPMailer\PHPMailer\PHPMailer;

require_once './app/model/Setting.php';

class EmailHelper
{
    private $aws_default_region, $aws_access_key_id, $aws_secret_access_key, $email_from_address;
    public function __construct()
    {
        $setting = new Setting();

        $list = $setting->findValue(["aws_access_key_id", "aws_secret_access_key", "aws_default_region", "email_from_address"]);

        $this->aws_access_key_id = $list['aws_access_key_id'];
        $this->aws_secret_access_key = $list['aws_secret_access_key'];
        $this->aws_default_region = $list['aws_default_region'];
        $this->email_from_address = $list['email_from_address'];
    }

    public function send($to_email, $subject, $body, $files = [])    {

        $client = new SesClient([
            'version' => 'latest',
            'region'  => $this->aws_default_region,
            'credentials' => [
                'key'    => $this->aws_access_key_id,
                'secret' => $this->aws_secret_access_key,
            ]
        ]);

        $sender_email = $this->email_from_address; // Must be a verified SES email
        $text_body = strip_tags($body);
        $charset = 'UTF-8';

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        // Set Sender and Recipient
        $mail->setFrom($sender_email, 'Terra Security'); // Must be verified in SES
        $mail->addAddress($to_email);

        // Content
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $body;
        $mail->AltBody = $text_body;

        // Add Attachments (Provide full system path and desired file display name)
        foreach($files as $file)
        {
            $mail->addAttachment($file, pathinfo($file, PATHINFO_BASENAME));
        }

        // 3. Compile and Extract the Raw MIME Message Data
        if (!$mail->preSend()) {
            die('MIME composition failed: ' . $mail->ErrorInfo);
        }
        $rawMimeMessage = $mail->getSentMIMEMessage();

        // 4. Send via Amazon SES sendRawEmail API
        try {
            $result = $client->sendRawEmail([
                'RawMessage' => [
                    'Data' => $rawMimeMessage,
                ],
            ]);
            
            return ["success" => true, "MessageId" => $result['MessageId']];
        } catch (AwsException $e) {
            return ["success" => false, "error" => $e->getAwsErrorMessage()];
        }
    }

}