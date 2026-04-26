<?php
/**
 * Export Controller - Handle Excel Exports
 * File: controllers/ExportController.php
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController {

    public function excel(): void {
        Auth::requireLogin();
        // Allow Staff, Kaprodi, and Superadmin
        if (!in_array(Auth::role(), [ROLE_STAFF, ROLE_KAPRODI, ROLE_SUPERADMIN])) {
            redirect('?page=403');
        }

        $db = getDB();
        $prodiId = null;

        // If Kaprodi, filter by their prodi
        if (Auth::role() === ROLE_KAPRODI) {
            $stmtK = $db->prepare("SELECT prodi_id FROM dosen WHERE email = ? LIMIT 1");
            $stmtK->execute([Auth::user()['email']]);
            $prodiId = $stmtK->fetchColumn();
        }

        $sql = "SELECT 
                    u.email, m.nim, u.nama, pj.judul,
                    d1.nama as p1, d2.nama as p2
                FROM pengajuan_judul pj
                JOIN mahasiswa m ON pj.user_id = m.user_id
                JOIN users u ON u.id = m.user_id
                LEFT JOIN prodi pr ON pr.id = m.prodi_id
                LEFT JOIN dosen d1 ON d1.id = pj.pembimbing_1_id
                LEFT JOIN dosen d2 ON d2.id = pj.pembimbing_2_id
                WHERE pj.status = 'selesai'";
        
        $params = [];
        if ($prodiId) {
            $sql .= " AND m.prodi_id = ?";
            $params[] = $prodiId;
        }

        $sql .= " ORDER BY pr.nama ASC, m.nim ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Judul Skripsi');

        // Header
        $headers = ['NO', 'Email', 'NIM', 'NAMA', 'JUDUL', 'PEMBIMBING 1', 'PEMBIMBING 2'];
        foreach ($headers as $k => $h) {
            $sheet->setCellValue([$k + 1, 1], $h);
        }

        // Style Header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');

        // Data
        $rowIdx = 2;
        foreach ($data as $i => $row) {
            $sheet->setCellValue('A' . $rowIdx, $i + 1);
            $sheet->setCellValue('B' . $rowIdx, $row['email']);
            $sheet->setCellValue('C' . $rowIdx, $row['nim']);
            $sheet->setCellValue('D' . $rowIdx, $row['nama']);
            $sheet->setCellValue('E' . $rowIdx, $row['judul']);
            $sheet->setCellValue('F' . $rowIdx, $row['p1']);
            $sheet->setCellValue('G' . $rowIdx, $row['p2']);
            $rowIdx++;
        }

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Daftar_Judul_Skripsi_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
