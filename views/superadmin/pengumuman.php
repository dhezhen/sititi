<?php
$pageTitle = 'Pengumuman';
require BASE_PATH . '/views/layouts/header.php';
$emailStatusLabels = [
    'menunggu' => ['Menunggu', 'secondary'],
    'tidak_diminta' => ['Tidak diminta', 'secondary'],
    'belum_dikonfigurasi' => ['Belum dikonfigurasi', 'warning'],
    'diterima' => ['Diterima server email', 'success'],
    'sebagian_gagal' => ['Sebagian gagal', 'warning'],
    'gagal' => ['Gagal', 'danger'],
];
?>

<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
        </ol></nav>
        <h1><i class="bi bi-megaphone me-2 text-primary"></i>Pengumuman</h1>
        <p class="mb-0">Kirim pengumuman sebagai notifikasi internal dan, jika diaktifkan, melalui email.</p>
    </div>
</div>

<?php renderFlash(); ?>

<div class="row g-4 align-items-start">
    <div class="col-lg-8">
        <section class="card" aria-labelledby="announcement-form-title">
            <div class="card-header">
                <h2 class="h5 mb-0" id="announcement-form-title">Buat pengumuman</h2>
            </div>
            <div class="card-body">
                <form action="?page=superadmin.pengumuman.store" method="POST" id="announcementForm">
                    <?php csrfField(); ?>

                    <div class="mb-4">
                        <label for="announcementTitle" class="form-label">Judul</label>
                        <input
                            id="announcementTitle"
                            type="text"
                            name="judul"
                            class="form-control"
                            maxlength="180"
                            required
                            autocomplete="off"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="announcementBody" class="form-label">Isi pengumuman</label>
                        <textarea
                            id="announcementBody"
                            name="isi"
                            class="form-control"
                            rows="8"
                            maxlength="10000"
                            required
                        ></textarea>
                        <div class="form-text">Maksimal 10.000 karakter. Isi yang sama akan tampil di notifikasi dan email.</div>
                    </div>

                    <fieldset class="mb-4">
                        <legend class="form-label mb-2">Penerima</legend>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="semua_pengguna" id="allActiveUsers" value="1" checked>
                            <label class="form-check-label fw-semibold" for="allActiveUsers">Semua pengguna aktif</label>
                        </div>
                        <?php if (!empty($roles)): ?>
                        <div class="row g-2 ms-1">
                            <?php foreach ($roles as $index => $role): ?>
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <input
                                        class="form-check-input recipient-role"
                                        type="checkbox"
                                        name="roles[]"
                                        id="recipientRole<?= $index ?>"
                                        value="<?= e($role['kode']) ?>"
                                    >
                                    <label class="form-check-label" for="recipientRole<?= $index ?>">
                                        <?= e($role['nama']) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div class="form-text mt-2">Matikan pilihan semua pengguna untuk memilih satu atau beberapa role.</div>
                    </fieldset>

                    <div class="border rounded p-3 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kirim_email" id="sendAnnouncementEmail" value="1" checked>
                            <label class="form-check-label fw-semibold" for="sendAnnouncementEmail">Kirim juga melalui email</label>
                        </div>
                        <div class="form-text ms-4">Setiap penerima mendapat email terpisah; alamat penerima lain tidak dibagikan.</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-2" aria-hidden="true"></i>Kirim Pengumuman
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <aside class="col-lg-4" aria-labelledby="email-status-title">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 card-title" id="email-status-title">Status email</h2>
                <?php if ($mailConfigured): ?>
                <p class="mb-2">Alamat pengirim: <strong><?= e($mailFromAddress) ?></strong></p>
                <p class="text-muted mb-0">Email diserahkan ke mail transport yang dikonfigurasi pada server PHP.</p>
                <?php else: ?>
                <p class="mb-2">Pengiriman email belum dikonfigurasi.</p>
                <?php if ($smtpEnabled): ?>
                <p class="text-muted mb-0">Periksa konfigurasi SMTP melalui menu Pengaturan Email. Notifikasi internal tetap dapat dikirim.</p>
                <?php else: ?>
                <p class="text-muted mb-0">Atur <code>MAIL_FROM_ADDRESS</code> pada server atau aktifkan SMTP melalui menu Pengaturan Email. Notifikasi internal tetap dapat dikirim.</p>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>

<section class="mt-5" aria-labelledby="announcement-history-title">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h4 mb-1" id="announcement-history-title">Riwayat pengumuman</h2>
            <p class="text-muted mb-0">Status email menunjukkan hasil penyerahan ke server, bukan konfirmasi email diterima kotak masuk.</p>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sititi align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Pengumuman</th>
                        <th scope="col">Penerima</th>
                        <th scope="col">Email</th>
                        <th scope="col">Pengirim</th>
                        <th scope="col">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pengumumanList)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada pengumuman. Gunakan formulir di atas untuk membuat pengumuman pertama.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pengumumanList as $item): ?>
                    <?php
                    $targetRoles = $item['target_roles'] ? json_decode($item['target_roles'], true) : [];
                    $targetNames = array_map(
                        static fn($code) => $roleNameByCode[$code] ?? $code,
                        is_array($targetRoles) ? $targetRoles : []
                    );
                    $emailStatus = $emailStatusLabels[$item['email_status']] ?? ['Status tidak diketahui', 'secondary'];
                    ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($item['judul']) ?></div>
                            <div class="small text-muted">
                                <?= empty($targetNames) ? 'Semua pengguna aktif' : e(implode(', ', $targetNames)) ?>
                            </div>
                        </td>
                        <td><?= number_format((int)$item['penerima_count']) ?></td>
                        <td>
                            <span class="badge bg-<?= e($emailStatus[1]) ?>"><?= e($emailStatus[0]) ?></span>
                            <?php if ((int)$item['email_diminta'] === 1 && in_array($item['email_status'], ['diterima', 'sebagian_gagal', 'gagal'], true)): ?>
                            <div class="small text-muted mt-1">
                                <?= number_format((int)$item['email_diterima']) ?> diterima server, <?= number_format((int)$item['email_gagal']) ?> gagal
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?= e($item['nama_pengirim'] ?? 'Akun dihapus') ?></td>
                        <td class="text-nowrap"><?= e(formatTanggal($item['created_at'], true)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
(() => {
    const allUsers = document.getElementById('allActiveUsers');
    const roleInputs = document.querySelectorAll('.recipient-role');
    const emailToggle = document.getElementById('sendAnnouncementEmail');
    const form = document.getElementById('announcementForm');

    const syncRecipientOptions = () => {
        roleInputs.forEach((input) => {
            input.disabled = allUsers.checked;
            if (allUsers.checked) input.checked = false;
        });
    };

    allUsers.addEventListener('change', syncRecipientOptions);
    syncRecipientOptions();

    form.addEventListener('submit', (event) => {
        if (allUsers.checked && emailToggle.checked) {
            const confirmed = window.confirm('Pengumuman ini akan dikirim ke semua pengguna aktif melalui notifikasi dan email. Lanjutkan?');
            if (!confirmed) event.preventDefault();
        }
    });
})();
</script>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
