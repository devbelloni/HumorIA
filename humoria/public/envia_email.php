<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

// Create a new PHPMailer instance
$mail = new PHPMailer();

// Tell PHPMailer to use SMTP
$mail->isSMTP();

// Enable SMTP debugging
// SMTP::DEBUG_OFF = off (for production use)
// SMTP::DEBUG_CLIENT = client messages
// SMTP::DEBUG_SERVER = client and server messages
// SMTPDebug = SMTP::DEBUG_SERVER;
// $mail->SMTP::DEBUG_OFF

// Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';

// Set the SMTP port number
$mail->Port = 465;

// Set the encryption mechanism to use
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

// Whether to use SMTP authentication
$mail->SMTPAuth = true;

// Username to use for SMTP authentication - use full email address for gmail
$mail->Username = 'prof.belloni@gmail.com';

// Password to use for SMTP authentication
$mail->Password = 'hbpxppywbzvwlals';

// Set who the message is to be sent from
$mail->setFrom('prof.belloni@gmail.com', 'Contato-Humor');

// Set who the message is to be sent to
$mail->addAddress('mbelloni@alumni.usp.br', 'Belloni');

// Set the subject line
$mail->Subject = 'Contato-Humor';

// Read an HTML message body from an external file, convert referenced images to embedded,
// convert HTML into a basic plain-text alternative body
$htmlBody = file_get_contents('contents.html');

// Check if the HTML body was read successfully
if ($htmlBody) {
    $mail->msgHTML($htmlBody, __DIR__);
} else {
    // Fallback to a default message if the file is not found
    $mail->Body = '<b>Nome: </b>' . $_POST['nome'] . '<br><b>Email: </b>' . $_POST['email'] . '<br><b>Mensagem: </b>' . $_POST['message'];
}

// Set the plain text body
$plainTextBody = isset($_POST['message']) ? $_POST['message'] : 'This is the plain text body';
$mail->AltBody = $plainTextBody;

// Send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo '<script>alert("Sua mensagem foi enviada!"); window.location.href = "form_contato.php";</script>';
//echo '<b>Nome: </b>' . $_POST['nome'] . '<br><b>Email: </b>' . $_POST['email'] . '<br><b>Mensagem: </b>' . $_POST['message'];
}

// Section 2: IMAP
// IMAP commands require the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
function save_mail($mail)
{
    // You can change 'Sent Mail' to any other folder or tag
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    // Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}
?>
