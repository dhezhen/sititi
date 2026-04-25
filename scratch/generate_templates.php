<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 1. Template Dosen
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Import Dosen');

$headers = ['nama', 'email', 'nidn', 'kode_prodi', 'bidang_keahlian', 'no_hp', 'jabatan_akademik', 'pendidikan', 'role', 'password'];
$data = [
    ['Dr. Andi Wijaya', 'andi@uniku.ac.id', '1122334455', 'TI', 'Kecerdasan Buatan', '082111222333', 'Lektor', 'S3', 'dewan', 'rahasia123'],
    ['Prof. Budi Rahardjo', 'budi.r@uniku.ac.id', '5544332211', 'SI', 'Sistem Enterprise', '082111222444', 'Profesor', 'S3', 'kaprodi', ''],
    ['Dr. Siti Aminah', 'siti@uniku.ac.id', '9988776655', 'DKV', 'Multimedia', '081234567890', 'Asisten Ahli', 'S2', 'dosen', 'nidn123'],
];

$sheet->fromArray($headers, NULL, 'A1');
$sheet->fromArray($data, NULL, 'A2');

// Auto size columns
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/../assets/templates/template_dosen.xlsx');

// 2. Template Mahasiswa
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Import Mahasiswa');

$headers = ['nama', 'email', 'nim', 'angkatan', 'semester', 'kode_prodi', 'no_hp', 'alamat'];
$data = [
    ['Budi Santoso', 'budi@uniku.ac.id', '2021001', '2021', '7', 'TI', '081234567890', 'Jl. Merdeka No. 1'],
    ['Siti Aminah', 'siti@uniku.ac.id', '2021002', '2021', '7', 'SI', '081234567891', 'Jl. Sudirman No. 2'],
];

$sheet->fromArray($headers, NULL, 'A1');
$sheet->fromArray($data, NULL, 'A2');

foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/../assets/templates/template_mahasiswa.xlsx');

echo "Excel templates generated successfully.\n";
