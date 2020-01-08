<?php
	require("PHPMailer.php");
	require("SMTP.php");
	require("Exception.php");
	require("info.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	class Email{			

		function sendEmail($to, $name, $subject, $body){ 
			$mail = new PHPMailer;
			$mail->isSMTP();

			$mail->SMTPDebug = SMTP::DEBUG_SERVER;

			$mail->Host = 'smtp.gmail.com';

			$mail->Port = 587;

			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

			$mail->SMTPAuth = true;

			$mail->Username = $email;

			$mail->Password = $pass;

			$mail->setFrom("thegurugamers@gmail.com", "CarMax CaptureIt");
			$mail->addReplyTo("no-reply@capit.io", "CarMax CaptureIt");
			$mail->addEmbeddedImage(dirname(__DIR__)."/img/logo.png", "logo", "logo.png");
			$mail->addAddress($to, $name);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->isHTML(true);
			if (!$mail->send()) {
				error_log($mail->ErrorInfo);
			}
		}
	}
?>