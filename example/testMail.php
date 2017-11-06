<?php

/**
 * Description of testMail
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-30 02:29:01
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\mail\PHPMailer;

$body = "测试MAIL ";
    try {
        $mail = new PHPMailer(true); //New instance, with exceptions enabled
        $mail->IsSMTP();                           // tell the class to use SMTP
        $mail->CharSet = 'utf-8';
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->Port = 25;                    // set the SMTP server port
        $mail->Host = "staffmail.joyme.com"; // SMTP server
        $mail->Username = "service@joyme.com";     // SMTP server username
        $mail->Password = "emsystem@jm1";            // SMTP server password
        $mail->IsSendmail();  // tell the class to use Sendmail
        $mail->AddReplyTo("service@joyme.com", "测试MAIL");
        $mail->From = "service@joyme.com";
        $mail->FromName = "系统测试邮件";
        $mail->AddAddress('clarkzhao@staff.joyme.com');
//        $mail->AddAddress('pengzhang@staff.joyme.com');
        $mail->Subject = "系统测试邮件 " . date('Ymd H:i:s', time());
        $mail->AltBody = ""; // optional, comment out and test
        $mail->WordWrap = 80; // set word wrap
        $mail->MsgHTML($body);
        $mail->IsHTML(true); // send as HTML
        $mail->Send();
        echo "Message has been sent ok\n";
    } catch (Exception $e) {
        echo $e->getMessage()."\n";
    }