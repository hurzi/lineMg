<?php
/**
* 发送邮件
* @param $sendto_email 收件箱
* @param $subject 主题
* @param $body 内容
* @param $nicheng 昵称随便填
* @param $fajianxiangUsername 发件箱
* @param $fajianxiangPassword 发件箱密码
* @return bool|null
*/
function phpmailer($sendto_email, $subject, $body, $nicheng, $fajianxiangUsername, $fajianxiangPassword){

    $phpmailer = dirname(__FILE__) . '/' . 'class.phpmailer.php';
    if (! file_exists ( $phpmailer )) {
    if (class_exists('Logger')) {
    Logger::error ( "phpmailer file not exist : " . $phpmailer );
    }
    return null;
    }
    $smtp = dirname(__FILE__) . '/' . 'class.smtp.php';
    if (! file_exists ( $smtp )) {
    if (class_exists('Logger')) {
    Logger::error ( "phpmailer file not exist : " . $smtp );
    }
    return null;
    }
    include_once ($phpmailer);
    include_once ($smtp);
    $mail = new PHPMailer();
    $mail->IsSMTP();                  // send via SMTP
    $mail->Host = 'smtp.'.substr($fajianxiangUsername,strrpos($fajianxiangUsername,'@')+1,strlen($fajianxiangUsername)); ;   // SMTP servers
    $mail->SMTPAuth = true;           // turn on SMTP authentication
    $mail->Username = $fajianxiangUsername;     // SMTP username  注意：普通邮件认证不需要加 @域名
    $mail->Password =  $fajianxiangPassword; // SMTP password
    $mail->From = $fajianxiangUsername;      // 发件人邮箱
    $mail->FromName =  $nicheng;  // 发件人
    $mail->CharSet = "GBK";   // 这里指定字符集！
    $mail->Encoding = "base64";
    $mail->AddAddress($sendto_email);  // 收件人邮箱
    $mail->IsHTML(true);  // send as HTML
    // 邮件主题
    $mail->Subject = iconv( "UTF-8", "gb18030//IGNORE" , $subject);
    // 邮件内容
    $mail->Body = '    <html><head>    <meta http-equiv="Content-Language" content="zh-cn">    <meta http-equiv="Content-Type" content="text/html; charset=GB18030">    </head>     <body>     '.iconv( "UTF-8", "gb18030//IGNORE" , $body).'     </body>      </html>     ';
    $mail->AltBody ="text/html";
    if(!$mail->Send())
    {

    return false;
    }
    else {

    return true;
    }

}
?>