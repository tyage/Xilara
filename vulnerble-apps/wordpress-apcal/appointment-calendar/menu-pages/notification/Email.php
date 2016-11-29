<?php
require_once('class.phpmailer.php');
if(!class_exists("SendEmail")) {
    class SendEmail
    {
        // notification to admin when new appointment arrived
        public function NotifyAdmin($HostName, $PortNo, $SmtpEmail, $Password, $AdminEmail, $AdminSubject, $AdminBody, $BlogName ) {
            $Subject = $AdminSubject;
            $Body = "<pre>".$AdminBody."</pre>";

            $mail = new PHPMailer();
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host       = $HostName;      // SMTP server
            $mail->SMTPDebug  = 1;              // enables SMTP debug information (for testing)// 1 = errors and messages , // 2 = messages only
            $mail->SMTPAuth   = true;           // enable SMTP authentication
            $mail->SMTPSecure = "ssl";          // sets the prefix to the server
            $mail->Host       = $HostName;      // sets G-MAIL as the SMTP server
            $mail->Port       = $PortNo;        // set the SMTP port for the G-MAIL server
            $mail->Username   = $SmtpEmail;     // G-MAIL username
            $mail->Password   = $Password;      // G-MAIL password
            $mail->SetFrom($AdminEmail, $BlogName);    //admin mail
            $mail->AddReplyTo($AdminEmail, $BlogName); // admin mail
            $mail->CharSet = 'UTF-8';
            $mail->Subject = mb_convert_encoding($Subject, "UTF-8", "auto");
            $mail->MsgHTML($Body);
            $mail->AddAddress($AdminEmail);    // sending email to
            $mail->Send();
        }


        // notify to client after booked an appointment
        public function NotifyClient($HostName, $PortNo, $SmtpEmail, $Password, $AdminEmail, $RecipientEmail, $RecipientSubject, $RecipientBody, $BlogName) {
            $Subject = $RecipientSubject;
            $To = $RecipientEmail;
            $Body = "<pre>".$RecipientBody."</pre>";
            $BlogName = $BlogName;

            $mail = new PHPMailer();
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host       = $HostName;      // SMTP server
            $mail->SMTPDebug  = 1;              // enables SMTP debug information (for testing)// 1 = errors and messages , // 2 = messages only
            $mail->SMTPAuth   = true;           // enable SMTP authentication
            $mail->SMTPSecure = "ssl";          // sets the prefix to the server
            $mail->Host       = $HostName;      // sets G-MAIL as the SMTP server
            $mail->Port       = $PortNo;        // set the SMTP port for the G-MAIL server
            $mail->Username   = $SmtpEmail;     // G-MAIL username
            $mail->Password   = $Password;              // G-MAIL password
            $mail->SetFrom($AdminEmail, $BlogName);    //admin mail
            $mail->AddReplyTo($AdminEmail, $BlogName); // admin mail
            $mail->CharSet = 'UTF-8';
            $mail->Subject = mb_convert_encoding($Subject, "UTF-8", "auto");
            $mail->MsgHTML($Body);
            $mail->AddAddress($To);                     //client email here
            $mail->Send();
        }
    }
}