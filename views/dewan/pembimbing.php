<?php
$pageTitle = 'Tentukan Pembimbing';
require BASE_PATH . '/views/layouts/header.php';
?>
<div class="page-header">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="?page=dewan">Review & Penentuan</a></li>
        <li class="breadcrumb-item active">Proses Review</li>
    </ol></nav>
    <h1><i class="bi bi-person-plus me-2 text-primary"></i>Review Proposal & Pembimbing</h1>
</div>
<?php renderFlash(); ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-file-earmark me-2 text-primary"></i>Data Pengajuan</span>
                <?= statusBadge($pengajuan['status']) ?>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-2">
                    <tr><td class="fw-semibold text-muted" style="width:130px">Mahasiswa</td>
                        <td><?= e($pengajuan['nama_mahasiswa']) ?> (<code><?= e($pengajuan['nim']) ?></code>)</td></tr>
                    <tr><td class="fw-semibold text-muted">Prodi</td>
                        <td><?= e($pengajuan['nama_prodi'] ?? '-') ?></td></tr>
                    <tr><td class="fw-semibold text-muted">Bidang</td>
                        <td><span class="badge bg-secondary"><?= e($pengajuan['bidang']) ?></span></td></tr>
                </table>
                <h6 class="fw-bold text-muted small mb-1">JUDUL</h6>
                <p class="fw-semibold text-primary mb-3"><?= e($pengajuan['judul']) ?></p>
                <h6 class="fw-bold text-muted small mb-1">RINGKASAN PROPOSAL</h6>
                <div class="p-3 rounded-3 bg-light small mb-3">
                    <?= (strpos($pengajuan['deskripsi'], '<') !== false) ? $pengajuan['deskripsi'] : nl2br(e($pengajuan['deskripsi'])) ?>
                </div>
                <?php if ($pengajuan['file_proposal']): ?>
                <a href="<?= UPLOAD_URL ?>/<?= e($pengajuan['file_proposal']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Lihat Proposal Lengkap
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-chat-left-dots me-2 text-primary"></i>Masukan Terdahulu</h6>
            </div>
            <div class="card-body pt-0">
                <div class="mb-3">
                    <label class="small text-muted fw-bold text-uppercase d-block mb-2">Masukan Calon Pembimbing</label>
                    <?php 
                    $catatanPembimbing = (new CatatanBimbingan())->getByPengajuanId($pengajuan['id']);
                    if (empty($catatanPembimbing)): ?>
                        <div class="alert alert-light border small py-1">Belum ada masukan dari pembimbing.</div>
                    <?php else: ?>
                        <?php foreach ($catatanPembimbing as $cp): ?>
                        <div class="p-2 rounded bg-light border-start border-primary border-3 mb-2 small" style="font-size: 0.85rem;">
                            <span class="fw-bold text-primary"><?= e($cp['nama_dosen']) ?>:</span> <?= nl2br(e($cp['catatan'])) ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mb-0">
                    <label class="small text-muted fw-bold text-uppercase d-block mb-2">Masukan Kaprodi</label>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 border-start border-primary border-3">
                        <div class="small fw-bold text-primary mb-1">Catatan Kaprodi:</div>
                        <div class="small text-dark">
                            <?= !empty($pengajuan['catatan_terakhir']) ? nl2br(e($pengajuan['catatan_terakhir'])) : '<em class="text-muted">Tidak ada catatan spesifik dari kaprodi.</em>' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-primary">
            <div class="card-header bg-primary text-white"><i class="bi bi-chat-text-fill me-2"></i>Review & Keputusan Dewan</div>
            <div class="card-body">
                <?php
                // Ambil usulan dari Kaprodi/Mahasiswa sebagai default
                $def1 = $pengajuan['pembimbing_1_id'];
                $def2 = $pengajuan['pembimbing_2_id'];
                ?>
                <form action="?page=dewan.proses" method="POST">
                    <?php csrfField(); ?>
                    <input type="hidden" name="pengajuan_id" value="<?= $pengajuan['id'] ?>">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tindakan / Aksi <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aksi" id="aksi_tetapkan" value="setujui" checked>
                                <label class="form-check-label fw-semibold text-primary" for="aksi_tetapkan">
                                    Setujui Proposal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aksi" id="aksi_revisi" value="revisi">
                                <label class="form-check-label fw-semibold text-warning" for="aksi_revisi">
                                    Kembalikan (Revisi Proposal)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan / Rekomendasi</label>
                        <div class="editor-container shadow-sm rounded-3">
                            <div class="editor" id="quill_catatan" data-input="catatan" style="min-height: 150px;"></div>
                        </div>
                        <input type="hidden" name="catatan" id="catatan">
                    </div>

                    <button type="submit" class="btn btn-primary px-4" id="btn-submit"
                            data-confirm="Simpan keputusan ini?">
                        <i class="bi bi-save me-2"></i>Simpan Keputusan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <?php if (!empty($pembimbingAktif)): ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-check me-2 text-success"></i>Pembimbing yang Diusulkan Kaprodi</div>
            <div class="card-body">
                <?php foreach ($pembimbingAktif as $pb): ?>
                <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded-3 border">
                    <div class="user-avatar" style="width:44px;height:44px;font-size:1.1rem;flex-shrink:0">
                        <?= strtoupper(substr($pb['nama_dosen'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-semibold"><?= e($pb['nama_dosen']) ?></div>
                        <div class="text-muted small">NIDN: <?= e($pb['nidn']) ?></div>
                        <span class="badge bg-primary mt-1">Pembimbing <?= $pb['urutan'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle me-1"></i>Kaprodi belum menetapkan pembimbing.
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><i class="bi bi-activity me-2 text-primary"></i>Riwayat Status</div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($logs as $log):
                        $dc=match($log['status_baru']){'disetujui','pembimbing_ditentukan'=>'success','ditolak'=>'danger','revisi'=>'warning',default=>''}; ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= $dc ?>"></div>
                        <div class="timeline-content">
                            <?= statusBadge($log['status_baru']) ?>
                            <?php if ($log['catatan']): ?>
                            <div class="small text-secondary mt-1">
                                <?= (strpos($log['catatan'], '<') !== false) ? $log['catatan'] : nl2br(e($log['catatan'])) ?>
                            </div>
                            <?php endif; ?>
                            <div class="timeline-meta mt-1"><?= e($log['nama_user']) ?> · <?= formatTanggal($log['created_at'], true) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const aksiTetapkan = document.getElementById('aksi_tetapkan');
    const aksiRevisi = document.getElementById('aksi_revisi');
    const btnSubmit = document.getElementById('btn-submit');
    const quillEditor = document.querySelector('#quill_catatan .ql-editor');

    function toggleFields() {
        if (aksiRevisi.checked) {
            // Ganti warna tombol
            btnSubmit.className = 'btn btn-warning px-4';
            btnSubmit.innerHTML = '<i class="bi bi-arrow-return-left me-2"></i>Kembalikan untuk Revisi';
        } else {
            // Reset tombol
            btnSubmit.className = 'btn btn-primary px-4';
            btnSubmit.innerHTML = '<i class="bi bi-check-circle me-2"></i>Setujui Proposal';
        }
    }

    aksiTetapkan.addEventListener('change', toggleFields);
    aksiRevisi.addEventListener('change', toggleFields);
    
    // Initial state
    toggleFields();
});
</script>


<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
