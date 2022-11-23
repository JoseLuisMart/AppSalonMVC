<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
       
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'] ;//'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Port = $_ENV['EMAIL_PORT']; //587; // 465 para SSL y 587 para tls
        $emailuser = $_ENV['EMAIL_USER'];
        $mail->Username = $emailuser; 
        $mail->Password = $_ENV['EMAIL_PASS']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //PHPMailer::ENCRYPTION_STARTTLS; // PHPMailer::ENCRYPTION_SMTPS para ssl, ENCRYPTION_STARTTLS para tls

        // Configurar el contenido del mail
        $mail->setFrom('$this->email'); // Quien envia el email // nuestro gmail, Nombre
        // Dirección de respuesta ->addReplyTo('correo', 'nombre')
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com'); // A quien se le envia el email // Pueden ser varios
        $mail->Subject = 'Confirma tu cuenta'; // Asunto

        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        // Definir el contenido
        $contenido = '<html>';
        $contenido .= '<p>Hola <strong>' . $this->nombre . '</strong> Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>';

        $contenido .= '<p> Presiona aquí: <a href="http://' . $_ENV['SERVER_URL'] . '/confirmar-cuenta?token=' . $this->token . '">Confirmar Cuenta</a> </p>';

        $contenido .= '<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje!</p>';
        $contenido .= '</html>';

        $mail->Body = $contenido;
        $mail->AltBody = 'Esto es texto alternativo sin HTML'; // Texto corto que aparece en los moviles antes de que se abra el correo electronico

        //Agregar algún adjunto
        //$mail->addAttachment(IMAGES_PATH.'logo.png');

        // Enviar el email
        $mail->send(); 

    }

    public function enviarInstrucciones() {

        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'] ;//'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Port = $_ENV['EMAIL_PORT']; //587; // 465 para SSL y 587 para tls
        $emailuser = $_ENV['EMAIL_USER'];
        $mail->Username = $emailuser; 
        $mail->Password = $_ENV['EMAIL_PASS']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Configurar el contenido del mail
        $mail->setFrom('$this->email'); // Quien envia el email // nuestro gmail, Nombre
        // Dirección de respuesta ->addReplyTo('correo', 'nombre')
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com'); // A quien se le envia el email // Pueden ser varios
        $mail->Subject = 'Reestablece tu Password'; // Asunto

        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        // Definir el contenido
        $contenido = '<html>';
        $contenido .= '<p>Hola <strong>' . $this->nombre . '</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>';

        $contenido .= '<p> Presiona aquí: <a href="http://' . $_ENV['SERVER_URL'] . '/recuperar?token=' . $this->token . '">Reestablecer Password</a> </p>';

        $contenido .= '<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje!</p>';
        $contenido .= '</html>';

        $mail->Body = $contenido;
        $mail->AltBody = 'Esto es texto alternativo sin HTML'; // Texto corto que aparece en los moviles antes de que se abra el correo electronico

        //Agregar algún adjunto
        //$mail->addAttachment(IMAGES_PATH.'logo.png');

        // Enviar el email
        $mail->send(); 
    }

}
