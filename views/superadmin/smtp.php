<?php
$pageTitle = 'Pengaturan Email';
require BASE_PATH . '/views/layouts/header.php';
?>

<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan Email</li>
        </ol></nav>
        <h1><i class="bi bi-envelope-gear me-2 text-primary" aria-hidden="true"></i>Pengaturan Email</h1>
        <p class="mb-0">Atur koneksi SMTP untuk email pengumuman dan reset kata sandi.</p>
    </div>
</div>

<?php renderFlash(); ?>

<div class="row g-4 align-items-start">
    <div class="col-lg-8">
        <section class="card" aria-labelledby="smtp-config-title">
            <div class="card-header">
                <h2 id="smtp-config-title" class="h5 mb-0">Konfigurasi server SMTP</h2>
            </div>
            <div class="card-body">
                <form action="?page=superadmin.smtp.save" method="POST">
                    <?php csrfField(); ?>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="enabled" id="smtpEnabled" value="1" <?= (int)$settings['enabled'] === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="smtpEnabled">Gunakan SMTP untuk email aplikasi</label>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="smtpHost" class="form-label">Host SMTP</label>
                            <input type="text" class="form-control" id="smtpHost" name="host" value="<?= e($settings['host']) ?>" maxlength="253" autocomplete="off" placeholder="smtp.domain.ac.id" required>
                        </div>
                        <div class="col-md-4">
                            <label for="smtpPort" class="form-label">Port</label>
                            <input type="number" class="form-control" id="smtpPort" name="port" value="<?= e($settings['port']) ?>" min="1" max="65535" required>
                        </div>
                        <div class="col-md-6">
                            <label for="smtpEncryption" class="form-label">Keamanan koneksi</label>
                            <select class="form-select" id="smtpEncryption" name="encryption" required>
                                <option value="tls" <?= $settings['encryption'] === 'tls' ? 'selected' : '' ?>>STARTTLS (umumnya port 587)</option>
                                <option value="ssl" <?= $settings['encryption'] === 'ssl' ? 'selected' : '' ?>>SSL/TLS (umumnya port 465)</option>
                                <option value="none" <?= $settings['encryption'] === 'none' ? 'selected' : '' ?>>Tanpa enkripsi (relay lokal tanpa autentikasi)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="smtpUsername" class="form-label">Nama pengguna SMTP</label>
                            <input type="text" class="form-control" id="smtpUsername" name="username" value="<?= e($settings['username']) ?>" maxlength="255" autocomplete="username">
                        </div>
                        <div class="col-12">
                            <label for="smtpPassword" class="form-label">Kata sandi SMTP</label>
                            <input type="password" class="form-control" id="smtpPassword" name="smtp_password" maxlength="2048" autocomplete="new-password" aria-describedby="smtpPasswordHelp">
                            <div id="smtpPasswordHelp" class="form-text">
                                <?php if ($settings['password_configured']): ?>Kata sandi tersimpan. Biarkan kosong untuk mempertahankannya.<?php else: ?>Isi jika server SMTP meminta autentikasi.<?php endif; ?>
                            </div>
                        </div>
                        <?php if ($settings['password_configured']): ?>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="clear_password" id="clearSmtpPassword" value="1">
                                <label class="form-check-label" for="clearSmtpPassword">Hapus kata sandi tersimpan</label>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <label for="smtpFromEmail" class="form-label">Alamat pengirim</label>
                            <input type="email" class="form-control" id="smtpFromEmail" name="from_email" value="<?= e($settings['from_email']) ?>" maxlength="150" autocomplete="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="smtpFromName" class="form-label">Nama pengirim</label>
                            <input type="text" class="form-control" id="smtpFromName" name="from_name" value="<?= e($settings['from_name']) ?>" maxlength="150" autocomplete="organization">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan pengaturan</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <aside class="col-lg-4" aria-labelledby="smtp-status-title">
        <section class="card">
            <div class="card-body">
                <h2 id="smtp-status-title" class="h5 card-title">Status pengiriman</h2>
                <?php if ($smtpConfigured): ?>
                <p class="mb-2"><span class="badge bg-success">Konfigurasi lengkap</span></p>
                <p class="mb-0">Email dikirim melalui <strong><?= e($settings['host']) ?>:<?= e($settings['port']) ?></strong> sebagai <?= e($settings['from_email']) ?>.</p>
                <?php elseif ((int)$settings['enabled'] === 1): ?>
                <p class="mb-2"><span class="badge bg-warning text-dark">Konfigurasi belum lengkap</span></p>
                <p class="mb-0">Lengkapi host, alamat pengirim, autentikasi, dan kunci enkripsi server untuk mengaktifkan SMTP.</p>
                <?php else: ?>
                <p class="mb-2"><span class="badge bg-secondary">SMTP nonaktif</span></p>
                <p class="mb-0">Email memakai mail transport PHP jika pengirim <code>MAIL_FROM_ADDRESS</code> tersedia.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="card mt-3" aria-labelledby="smtp-test-title">
            <div class="card-body">
                <h2 id="smtp-test-title" class="h5 card-title">Kirim email percobaan</h2>
                <p class="text-muted">Simpan konfigurasi terlebih dahulu. Email percobaan memeriksa koneksi, autentikasi, dan pengiriman ke alamat tujuan.</p>
                <form action="?page=superadmin.smtp.test" method="POST">
                    <?php csrfField(); ?>
                    <div class="mb-3">
                        <label for="smtpTestEmail" class="form-label">Alamat email tujuan</label>
                        <input type="email" id="smtpTestEmail" name="test_email" class="form-control" autocomplete="email" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100" <?= !$smtpConfigured ? 'disabled' : '' ?>>Kirim email percobaan</button>
                </form>
            </div>
        </section>

        <div class="alert alert-info mt-3 mb-0" role="note">
            <strong>Kunci enkripsi server</strong>
            <p class="mb-2">Kata sandi SMTP dienkripsi di database. Tetapkan <code>SMTP_ENCRYPTION_KEY</code> dengan nilai acak minimal 32 karakter pada environment server.</p>
            <?php if (!$settings['encryption_key_configured']): ?>
            <p class="mb-0">Kunci belum terdeteksi. Simpan kunci sebelum memasukkan kata sandi SMTP.</p>
            <?php else: ?>
            <p class="mb-0">Kunci enkripsi terdeteksi. Simpan nilainya di server yang sama agar kata sandi tetap dapat dibaca setelah deployment.</p>
            <?php endif; ?>
        </div>
    </aside>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
