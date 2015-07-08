<!--

manifold-impact-analytics
https://github.com/braunsg/manifold-impact-analytics

Open source code for Manifold, an automated impact analytics and visualization platform developed by
Steven Braun.

COPYRIGHT (C) 2015 Steven Braun

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

A full copy of the license is included in LICENSE.md.

//////////////////////////////////////////////////////////////////////////////////////////
/////// About this file

Handler for sending messages through the contact form

-->

<?php
include("../../config/default-config.php");
include("../../functions/default-functions.php");

$con = connectDB();

$name = mysqli_real_escape_string($con,strip_tags($_POST["name"]));
$email = mysqli_real_escape_string($con,strip_tags($_POST["email"]));
$message = mysqli_real_escape_string($con, strip_tags($_POST["message"]));

// Inject the message content into the database for tracking
$message_sql = "INSERT INTO contact_messages (contact_name, contact_email, contact_message) VALUES ('$name','$email','$message')";

if(runQuery($con,$message_sql)) {

	require '../../libraries/PHPMailer/PHPMailerAutoload.php';

	// Send confirmation message to provided e-mail

	$confirmation_mail = new PHPMailer;

	$confirmation_mail ->isSMTP();                                      // Set mailer to use SMTP
	$confirmation_mail ->Host = $smtp['host'];  // Specify main and backup SMTP servers
	$confirmation_mail ->SMTPAuth = True;                               // Enable SMTP authentication
	$confirmation_mail ->Username = $smtp['user'];                 // SMTP username
	$confirmation_mail ->Password = $smtp['pw'];                           // SMTP password
	$confirmation_mail ->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted 

	$confirmation_mail ->From = 'manifold-alias@example.edu';
	$confirmation_mail ->FromName = 'Manifold Contact';
	$confirmation_mail ->addAddress($email);     // Add a recipient

	$confirmation_mail ->isHTML(true);                                  // Set email format to HTML

	$confirmation_mail ->Subject = 'Manifold: Contact form confirmation';
	$confirmation_mail ->Body    = 
		"This message is a confirmation that we have received your inquiry provided through the Manifold contact form. Your message details are below. We will respond to your inquiry as soon as possible (typically within 3-5 days). Please do not respond to this e-mail.<br><br>" .
		"<b>Name</b>: $name<br>" .
		"<b>E-mail</b>: $email<br>" .
		"<b>Question</b>:<br><br>" .  preg_replace("/\r\n|\r|\n/",'<br/>',$message);

	// Send e-mail to mailing list

	$listserv_mail = new PHPMailer;

	$listserv_mail->isSMTP();                                      // Set mailer to use SMTP
	$listserv_mail->Host = $smtp['host'];  // Specify main and backup SMTP servers
	$listserv_mail->SMTPAuth = True;                               // Enable SMTP authentication
	$listserv_mail->Username = $smtp['user'];                 // SMTP username
	$listserv_mail->Password = $smtp['pw'];                           // SMTP password
	$listserv_mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted 

	$listserv_mail->From = 'manifold-alias@example.edu';
	$listserv_mail->FromName = 'Manifold Contact';
	$listserv_mail->addAddress('manifold-alias@example.edu');     // Add a recipient

	$listserv_mail->isHTML(true);                                  // Set email format to HTML

	$listserv_mail->Subject = 'Manifold: New message through contact form';
	$listserv_mail->Body    = 
		"A new message has been submitted through the Manifold contact form. <br><br>" .
		"<b>Name</b>: $name<br>" .
		"<b>E-mail</b>: $email<br>" .
		"<b>Question</b>:<br><br>" .  preg_replace("/\r\n|\r|\n/",'<br/>',$message);
	
	
// 	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$listserv_mail->send()) {
		print "An error has occurred and your message was not sent. Please try again.";
	} else {
		$confirmation_mail->send();
		print "Message sent successfully. You should receive a confirmation message sent to the e-mail you provided. Someone will respond to your inquiry as quickly as possible.";
	}


} else {
		print "An error has occurred and your message was not sent. Please try again.";
}

closeDB($con);

?>
