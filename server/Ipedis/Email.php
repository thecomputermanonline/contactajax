<?php
/**
 * File: Email.php
 * User: mofasa
 * Date: 26/02/15 14:39
 */

namespace Ipedis;


class Email {
    /**
     * @return PHPMailer
     */
    const CONFIGPATH = "../config/email.php";

    /**
     * @return mixed
     * @throws RouterException
     */
    public function getConfig(){
        if(is_file(self::CONFIGPATH))
            throw new \Ipedis\RouterException("Config file not found ".__DIR__."/".self::CONFIGPATH,500);
        return include(__DIR__."/".self::CONFIGPATH);
    }
    public function mailFactory(){
        $config = $this->getConfig();
        $mail = new \PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $config["host"];
        $mail->SMTPAuth = true;
        $mail->Username = $config["username"];
        $mail->Password = $config["password"];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->IsHTML(true);
        return $mail;
    }

    /**
     * @param PHPMailer $mail
     * @return PHPMailer
     */
    public function configureMail(\PHPMailer $mail){
        $config = $this->getConfig();
        foreach($config["emails_to"] as $email){
            $mail->addAddress($email);
        }
        return $mail;
    }

    public function buildMessage($parameter){
        $message = 'Vous venez de recevoir une demande de contact depuis '.\Ipedis\Sanitarize::string($parameter["email"]).' ( '.date("d-m-Y").' )';
        $message .= "\n\n------------------------------------------------------------------------------------------\n\n";
        $message .= "<strong>Nom / Prénom : </strong>".\Ipedis\Sanitarize::string($parameter["name"])."\n\n";
        $message .= "<strong>Email : </strong>".\Ipedis\Sanitarize::string($parameter["email"])."\n\n";
        $message .= "<strong>sujet : </strong>".\Ipedis\Sanitarize::string($parameter["sujet"])."\n\n";
        $message .= "<strong>Message : </strong>\n\n".\Ipedis\Sanitarize::string($parameter["message"])."\n\n";
        return $message;
    }
}