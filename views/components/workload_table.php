<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
        <span><i class="bi bi-person-lines-fill me-2 text-primary"></i>Rekap Beban Bimbingan Dosen</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover table-sititi mb-0 align-middle">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Dosen</th>
                        <th class="text-center">Total Bimbingan</th>
                        <th>Rincian per Angkatan (Maks <?= MAX_PEMBIMBING_PER_ANGKATAN ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workloadDosen)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data dosen atau bimbingan.</td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($workloadDosen as $wd): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="fw-semibold text-primary"><?= e($wd['nama']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary fs-6"><?= $wd['total_semua'] ?></span>
                            </td>
                            <td>
                                <?php if (empty($wd['per_angkatan'])): ?>
                                    <span class="text-muted small">Belum ada bimbingan aktif</span>
                                <?php else: ?>
                                    <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($wd['per_angkatan'] as $angkatan => $total): 
                                        $percent = ($total / MAX_PEMBIMBING_PER_ANGKATAN) * 100;
                                        $colorClass = 'success';
                                        if ($percent >= 90) $colorClass = 'danger';
                                        elseif ($percent >= 70) $colorClass = 'warning';
                                    ?>
                                        <div class="border rounded px-2 py-1 bg-light border-<?= $colorClass ?> border-opacity-50" style="min-width: 100px;">
                                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.7rem;">
                                                <span class="fw-bold text-muted">Angkatan <?= $angkatan ?></span>
                                                <span class="text-<?= $colorClass ?> fw-bold"><?= $total ?>/<?= MAX_PEMBIMBING_PER_ANGKATAN ?></span>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-<?= $colorClass ?>" style="width: <?= $percent ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
