<?php

class SmtpClient {

    public static function send(array $settings, string $to, string $subject, string $body): bool {
        $socket = self::connect($settings);
        try {
            self::authenticate($socket, $settings);
            self::command($socket, 'MAIL FROM:<' . $settings['from_email'] . '>', [250]);
            self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::command($socket, 'DATA', [354]);

            $fromName = '=?UTF-8?B?' . base64_encode($settings['from_name'] !== '' ? $settings['from_name'] : APP_NAME) . '?=';
            $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $domain = substr(strrchr($settings['from_email'], '@') ?: '@localhost', 1);
            $headers = [
                'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000',
                'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
                'From: ' . $fromName . ' <' . $settings['from_email'] . '>',
                'To: <' . $to . '>',
                'Subject: ' . $subject,
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                'Content-Transfer-Encoding: base64',
            ];
            $encodedBody = chunk_split(base64_encode(str_replace(["\r\n", "\r"], "\n", $body)), 76, "\r\n");
            $message = implode("\r\n", $headers) . "\r\n\r\n" . $encodedBody;
            $message = preg_replace('/(^|\r\n)\./', '$1..', $message);
            self::write($socket, rtrim($message, "\r\n") . "\r\n.\r\n");
            self::expect($socket, [250]);
            try {
                self::command($socket, 'QUIT', [221]);
            } catch (Throwable $e) {
                error_log('Email sudah diterima server SMTP, tetapi koneksi tidak ditutup dengan normal.');
            }
            return true;
        } finally {
            fclose($socket);
        }
    }

    private static function connect(array $settings) {
        $host = trim((string)$settings['host']);
        $port = (int)$settings['port'];
        $tls = $settings['encryption'] === 'ssl';
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'peer_name' => $host,
                'SNI_enabled' => true,
            ],
        ]);
        $targetHost = str_contains($host, ':') ? '[' . $host . ']' : $host;
        $target = ($tls ? 'ssl://' : 'tcp://') . $targetHost . ':' . $port;
        $socket = @stream_socket_client($target, $errorCode, $errorMessage, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            throw new RuntimeException('Koneksi SMTP gagal: ' . $errorMessage . ' (' . $errorCode . ')');
        }
        try {
            stream_set_timeout($socket, 15);
            self::expect($socket, [220]);
            self::command($socket, 'EHLO ' . self::clientName(), [250]);
            if ($settings['encryption'] === 'tls') {
                self::command($socket, 'STARTTLS', [220]);
                $cryptoResult = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if ($cryptoResult !== true) throw new RuntimeException('Negosiasi TLS dengan server SMTP gagal.');
                self::command($socket, 'EHLO ' . self::clientName(), [250]);
            }
        } catch (Throwable $e) {
            fclose($socket);
            throw $e;
        }
        return $socket;
    }

    private static function authenticate($socket, array $settings): void {
        if ($settings['username'] === '') return;
        if (!is_string($settings['password']) || $settings['password'] === '') {
            throw new RuntimeException('Kata sandi SMTP tidak tersedia. Periksa SMTP_ENCRYPTION_KEY.');
        }
        self::command($socket, 'AUTH LOGIN', [334]);
        self::command($socket, base64_encode($settings['username']), [334]);
        self::command($socket, base64_encode($settings['password']), [235]);
    }

    private static function command($socket, string $command, array $expected): string {
        self::write($socket, $command . "\r\n");
        return self::expect($socket, $expected);
    }

    private static function expect($socket, array $expected): string {
        $response = '';
        do {
            $line = fgets($socket, 516);
            if ($line === false) throw new RuntimeException('Server SMTP menutup koneksi sebelum memberi respons.');
            $response .= $line;
            $meta = stream_get_meta_data($socket);
            if ($meta['timed_out']) throw new RuntimeException('Server SMTP melewati batas waktu respons.');
        } while (strlen($line) > 3 && $line[3] === '-');

        $code = (int)substr($line, 0, 3);
        if (!in_array($code, $expected, true)) {
            throw new RuntimeException('Server SMTP menolak perintah dengan kode ' . $code . '.');
        }
        return $response;
    }

    private static function write($socket, string $data): void {
        $length = strlen($data);
        $written = 0;
        while ($written < $length) {
            $result = fwrite($socket, substr($data, $written));
            if ($result === false || $result === 0) throw new RuntimeException('Data tidak dapat dikirim ke server SMTP.');
            $written += $result;
        }
    }

    private static function clientName(): string {
        $name = gethostname() ?: 'localhost';
        return preg_match('/^[A-Za-z0-9.-]+$/', $name) ? $name : 'localhost';
    }
}
