<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

function meeting_mail_admin_email(): string
{
    return 'okprincesingh@gmail.com';
}

function meeting_mail_from_email(): string
{
    return 'website@jaikvik.com';
}

function meeting_google_meet_link(): string
{
    $link = getenv('MEETING_GOOGLE_MEET_LINK');
    if (!is_string($link) || trim($link) === '') {
        $link = 'https://meet.google.com/';
    }
    return trim($link);
}

function meeting_mail_last_error(?string $set = null): string
{
    static $lastError = '';
    if ($set !== null) {
        $lastError = $set;
    }
    return $lastError;
}

function meeting_send_html_mail(string $to, string $subject, string $htmlBody): bool
{
    meeting_mail_last_error('');
    $to = trim($to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        meeting_mail_last_error('Invalid recipient email.');
        return false;
    }

    if (!class_exists(PHPMailer::class)) {
        meeting_mail_last_error('PHPMailer is not available in vendor/autoload.php.');
        return false;
    }

    $host = 'smtp.hostinger.com';
    $username = 'website@jaikvik.com';
    $password = '$$Email@123#';
    $port = 587;
    $encryption = PHPMailer::ENCRYPTION_STARTTLS;

    $from = meeting_mail_from_email();
    $fromName = trim((string) getenv('MAIL_FROM_NAME'));
    if ($fromName === '') {
        $fromName = 'MyBrandPlease';
    }

    try {
        $mail = new PHPMailer(true);
        $mail->Timeout = 20;
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->SMTPSecure = $encryption;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);
        $mail->addReplyTo($from, $fromName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)));
        $mail->send();
        meeting_mail_last_error('SMTP accepted for: ' . $to);
        return true;
    } catch (Exception $e) {
        $msg = $e->getMessage();
        meeting_mail_last_error($msg);
        error_log('[meeting_mailer] send failed to ' . $to . ': ' . $msg);
        return false;
    }
}
