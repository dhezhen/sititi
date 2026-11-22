<?php

class SMTPSettings {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function get(): array {
        $stmt = $this->db->query('SELECT * FROM smtp_settings WHERE id = 1 LIMIT 1');
        $settings = $stmt->fetch() ?: [
            'enabled' => 0,
            'host' => '',
            'port' => 587,
            'encryption' => 'tls',
            'username' => '',
            'password_encrypted' => null,
            'from_email' => '',
            'from_name' => APP_NAME,
        ];
        $settings['password_configured'] = !empty($settings['password_encrypted']);
        $settings['password'] = $settings['password_configured']
            ? $this->decryptPassword($settings['password_encrypted'])
            : '';
        $settings['encryption_key_configured'] = $this->encryptionKey() !== null;
        return $settings;
    }

    public function save(array $data, ?string $newPassword, bool $clearPassword): void {
        $current = $this->get();
        $encryptedPassword = $current['password_encrypted'];
        if ($newPassword !== null && $newPassword !== '') {
            $encryptedPassword = $this->encryptPassword($newPassword);
        } elseif ($clearPassword) {
            $encryptedPassword = null;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO smtp_settings
                (id, enabled, host, port, encryption, username, password_encrypted, from_email, from_name, updated_at)
             VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                enabled = VALUES(enabled), host = VALUES(host), port = VALUES(port),
                encryption = VALUES(encryption), username = VALUES(username),
                password_encrypted = VALUES(password_encrypted), from_email = VALUES(from_email),
                from_name = VALUES(from_name), updated_at = NOW()'
        );
        $stmt->execute([
            (int)$data['enabled'],
            $data['host'],
            (int)$data['port'],
            $data['encryption'],
            $data['username'],
            $encryptedPassword,
            $data['from_email'],
            $data['from_name'],
        ]);
    }

    public function isEnabled(): bool {
        $settings = $this->get();
        return (int)$settings['enabled'] === 1;
    }

    public function isConfigured(): bool {
        $settings = $this->get();
        $hostValid = preg_match('/^[A-Za-z0-9.-]+$/', (string)$settings['host']) === 1
            && !str_contains((string)$settings['host'], '..')
            && !str_starts_with((string)$settings['host'], '.')
            && !str_ends_with((string)$settings['host'], '.');
        if ((int)$settings['enabled'] !== 1
            || !$hostValid
            || (int)$settings['port'] < 1
            || (int)$settings['port'] > 65535
            || !filter_var($settings['from_email'], FILTER_VALIDATE_EMAIL)
            || !in_array($settings['encryption'], ['tls', 'ssl', 'none'], true)
            || ($settings['encryption'] === 'none' && $settings['username'] !== '')
            || (!$settings['encryption_key_configured'] && $settings['password_configured'])) {
            return false;
        }
        return $settings['username'] === '' || ($settings['password_configured'] && $settings['password'] !== false);
    }

    private function encryptionKey(): ?string {
        $key = getenv('SMTP_ENCRYPTION_KEY');
        if ($key === false) $key = $_SERVER['SMTP_ENCRYPTION_KEY'] ?? '';
        $key = trim((string)$key);
        return strlen($key) >= 32 ? hash('sha256', $key, true) : null;
    }

    private function encryptPassword(string $password): string {
        $key = $this->encryptionKey();
        if ($key === null) {
            throw new RuntimeException('SMTP_ENCRYPTION_KEY harus berisi minimal 32 karakter.');
        }
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($password, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) throw new RuntimeException('Kata sandi SMTP tidak dapat dienkripsi.');
        return base64_encode($iv . $tag . $ciphertext);
    }

    private function decryptPassword(string $payload): string|false {
        $key = $this->encryptionKey();
        $data = base64_decode($payload, true);
        if ($key === null || $data === false || strlen($data) < 28) return false;
        return openssl_decrypt(
            substr($data, 28),
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            substr($data, 0, 12),
            substr($data, 12, 16)
        );
    }
}
