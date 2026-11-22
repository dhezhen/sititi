<?php

class Mailer {

    public static function fromAddress(): string {
        $smtpSettings = new SMTPSettings();
        $smtp = $smtpSettings->get();
        if ((int)$smtp['enabled'] === 1) return trim((string)$smtp['from_email']);

        $address = getenv('MAIL_FROM_ADDRESS');
        if ($address === false) {
            $address = $_SERVER['MAIL_FROM_ADDRESS'] ?? '';
        }
        return trim((string)$address);
    }

    public static function fromName(): string {
        $smtpSettings = new SMTPSettings();
        $smtp = $smtpSettings->get();
        if ((int)$smtp['enabled'] === 1 && trim((string)$smtp['from_name']) !== '') {
            return trim(str_replace(["\r", "\n"], ' ', (string)$smtp['from_name']));
        }

        $name = getenv('MAIL_FROM_NAME');
        if ($name === false || trim((string)$name) === '') {
            $name = APP_NAME;
        }
        return trim(str_replace(["\r", "\n"], ' ', (string)$name));
    }

    public static function isConfigured(): bool {
        $smtpSettings = new SMTPSettings();
        if ($smtpSettings->isEnabled()) return $smtpSettings->isConfigured();

        return function_exists('mail')
            && filter_var(self::fromAddress(), FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function send(string $to, string $subject, string $body): bool {
        $to = trim($to);
        $smtpSettings = new SMTPSettings();
        if ($smtpSettings->isEnabled()) {
            if (!$smtpSettings->isConfigured() || filter_var($to, FILTER_VALIDATE_EMAIL) === false) return false;
            try {
                return SmtpClient::send($smtpSettings->get(), $to, $subject, $body);
            } catch (Throwable $e) {
                error_log('Pengiriman email SMTP gagal: ' . $e->getMessage());
                return false;
            }
        }

        $from = self::fromAddress();
        if (!self::isConfigured() || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedName = '=?UTF-8?B?' . base64_encode(self::fromName()) . '?=';
        $headers = [
            'From: ' . $encodedName . ' <' . $from . '>',
            'Reply-To: ' . $from,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: SiTiTi'
        ];

        try {
            return @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
        } catch (Throwable $e) {
            error_log('Email pengumuman gagal: ' . $e->getMessage());
            return false;
        }
    }
}
