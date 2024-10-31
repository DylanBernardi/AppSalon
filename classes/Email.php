<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public $email;
    public $nombre;
    public $token;
    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        //Crear objeto
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        //Contenido del mail
        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
        $mail->Subject = 'Confirma tu cuenta';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong></p> Has creado tu cuenta en App Salon, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p> Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "' >Confirmar Cuenta </a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar este mensaje";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        $mail->send();
    }

    public function enviarInstrucciones()
    {

        //Crear objeto
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        //Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        //Contenido del mail
        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
        $mail->Subject = 'Restablece tu contraseña';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong></p> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p> Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "' >Reestablecer password</a> </p>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar este mensaje";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        $mail->send();
    }
}
