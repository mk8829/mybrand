<?php

require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../PHPMailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
}

function meeting_mail_admin_email(): string
{
    $email = getenv('MEETING_MAIL_ADMIN') ?: getenv('MAIL_ADMIN_ADDRESS');
    return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'okprincesingh@gmail.com';
}

function meeting_mail_from_email(): string
{
    $email = getenv('MEETING_MAIL_FROM') ?: getenv('MAIL_FROM_ADDRESS');
    return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'website@jaikvik.com';
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

    $host = getenv('MEETING_SMTP_HOST') ?: (getenv('SMTP_HOST') ?: 'smtp.hostinger.com');
    $username = getenv('MEETING_SMTP_USERNAME') ?: (getenv('SMTP_USERNAME') ?: 'website@jaikvik.com');
    $password = getenv('MEETING_SMTP_PASSWORD') ?: (getenv('SMTP_PASSWORD') ?: '$$Email@123#');
    $port = (int) (getenv('MEETING_SMTP_PORT') ?: (getenv('SMTP_PORT') ?: 587));
    $encryption = getenv('MEETING_SMTP_ENCRYPTION') ?: (getenv('SMTP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS);

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
        $mail->SMTPAutoTLS = true;
        if (meeting_mail_allows_relaxed_tls()) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }
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

function meeting_mail_allows_relaxed_tls(): bool
{
    $value = getenv('MEETING_SMTP_RELAX_TLS');
    if (!is_string($value) || $value === '') {
        $value = getenv('SMTP_RELAX_TLS');
    }
    if (is_string($value) && $value !== '') {
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    $server = strtolower((string) ($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? ''));
    return in_array($server, ['localhost', '127.0.0.1', '::1'], true)
        || str_starts_with($server, 'localhost:')
        || str_starts_with($server, '127.0.0.1:');
}
