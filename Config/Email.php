<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Email
{
    public static function sendActivation($to, $name, $link)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'econutri.app@gmail.com';
            $mail->Password = 'tieg xmbj mric dukr';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Expéditeur
            $mail->setFrom('econutri.app@gmail.com', 'EcoNutri');

            // Destinataire
            $mail->addAddress($to, $name);

            // Email
            $mail->isHTML(true);
            $mail->Subject = "Activation de votre compte EcoNutri";

            $mail->Body = "
                <h2>Bienvenue $name 👋</h2>
                <p>Clique sur le lien pour activer ton compte :</p>
                <a href='$link'>Activer mon compte</a>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Mail error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
