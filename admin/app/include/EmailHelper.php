<?php
namespace App;

use App\Model\Setting;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

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

    public function send($to_email, $subject, $body)    {

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

        try {
            $result = $client->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$to_email],
                ],
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $charset,
                            'Data' => $body,
                        ],
                        'Text' => [
                            'Charset' => $charset,
                            'Data' => $text_body,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => $charset,
                        'Data' => $subject,
                    ],
                ],
                'Source' => $sender_email,
            ]);
            
            return ["success" => true, "MessageId" => $result['MessageId']];
        } catch (AwsException $e) {

            return ["success" => false, "error" => $e->getAwsErrorMessage()];
        }
    }

}