<?php 

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendEmail
{
    private $email;

    public function __construct()
    {
        $this->email = new PHPMailer(true);
        $this->serverConfig();
    }

    private function serverConfig()
    {
        $this->email->SMTPDebug  = SMTP::DEBUG_SERVER;
        $this->email->isSMTP();
        $this->email->Host       = 'mail.atlas-ia.com';
        $this->email->SMTPAuth   = true;
        $this->email->Username   = 'teste@atlas-ia.com';
        $this->email->Password   = '123456789';
        $this->email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->email->Port       = 465;    
    }

    public function setAdrress($from, $to)
    {
        $this->email->setFrom($from, 'Atlas-ia');
        $this->email->addAddress($to);

        return $this;
    }

    public function setBody($subject, $body)
    {
        $this->email->isHTML(true);
        $this->email->Subject = $subject;
        $this->email->Body    = $body;

        return $this;
    }

    public function send()
    {
        $this->email->send();

        return $this;
    }

    public function getInstance()
    {
        return $this->email;
    }
}