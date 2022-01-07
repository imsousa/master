<?php

	function sendMail($mail_add, $body, $assunto){
		require_once ('PHPMailer_v5.0.2/class.phpmailer.php');
		$mail = new PHPMailer(); 
		$mail->IsHTML(true);
		$mail->SetFrom("suporte@fc.ul.pt", "Suporte");
		$mail->AddAddress($mail_add);
		$mail->Subject  =  $assunto;
		$body = $body;
		$mail->MsgHTML($body);
		return $mail->Send();
	}
	

?>