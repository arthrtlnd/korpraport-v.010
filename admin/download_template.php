<?php
session_start();
require_once '../app/koneksi.php';
check_admin();

// Load library
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

// Cek ekstensi ZIP
if (!extension_loaded('zip')) {
    die("Error: Ekstensi PHP 'zip' belum aktif. Tidak bisa generate file Excel.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// --- 1. SETUP HEADER ---
// REVISI: Header disesuaikan (Nama Korp/Matra/Satker, Tambah Gender)
$headers = [
    'A1' => 'NRP',
    'B1' => 'Nama',
    'C1' => 'NIK (Wajib 16 Angka)',
    'D1' => 'Jenis Kelamin (L/P)',
    'E1' => 'Tempat Lahir',
    'F1' => 'Tanggal Lahir (YYYY-MM-DD)',
    'G1' => 'Kode Pangkat', // Tetap Kode agar akurat (misal 73, 81)
    'H1' => 'Korp (Sebutan)', // Ubah jadi Sebutan (misal Inf, Kav)
    'I1' => 'Matra (Nama)',   // Ubah jadi Nama (misal TNI AD)
    'J1' => 'Satuan Kerja Baru (Nama)', // Ubah jadi Nama Satker
    'K1' => 'Satuan Kerja Lama (Teks)'  // Ubah jadi Teks bebas
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
    $sheet->getStyle($cell)->getFont()->setBold(true);
    $sheet->getStyle($cell)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFCCCCCC'); 
}

// --- 2. DATA CONTOH (DUMMY) ---
// Baris 2: Contoh Data
$sheet->setCellValueExplicit('A2', '111111', DataType::TYPE_STRING);
$sheet->setCellValue('B2', 'CONTOH PERSONEL AD');
$sheet->setCellValueExplicit('C2', '3175000000000001', DataType::TYPE_STRING);
$sheet->setCellValue('D2', 'L'); // Gender
$sheet->setCellValue('E2', 'Jakarta');
$sheet->setCellValue('F2', '1990-01-01');
$sheet->setCellValueExplicit('G2', '73', DataType::TYPE_STRING); // Kapten (Kode)
$sheet->setCellValue('H2', 'Arm'); // Korp Sebutan
$sheet->setCellValue('I2', 'TNI AD'); // Matra Nama
$sheet->setCellValue('J2', 'PUSINFOLAHTA TNI'); // Satker Nama
$sheet->setCellValue('K2', 'ITJEN TNI'); // Satker Lama (Teks)

// Baris 3: Contoh Data 2
$sheet->setCellValueExplicit('A3', '222222', DataType::TYPE_STRING);
$sheet->setCellValue('B3', 'CONTOH PERSONEL AL');
$sheet->setCellValueExplicit('C3', '3175000000000002', DataType::TYPE_STRING);
$sheet->setCellValue('D3', 'P'); // Gender
$sheet->setCellValue('E3', 'Surabaya');
$sheet->setCellValue('F3', '1992-05-20');
$sheet->setCellValueExplicit('G3', '81', DataType::TYPE_STRING); // Mayor (Kode)
$sheet->setCellValue('H3', 'K'); // Korp Sebutan
$sheet->setCellValue('I3', 'TNI AL'); // Matra Nama
$sheet->setCellValue('J3', 'ITJEN TNI'); 
$sheet->setCellValue('K3', 'SMIN PANGLIMA TNI'); 

// --- 3. FORMATTING ---
$sheet->getStyle('A:A')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$sheet->getStyle('C:C')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('yyyy-mm-dd');

// Auto width
foreach(range('A','K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// --- 4. OUTPUT DOWNLOAD ---
$filename = 'Template_Import_Personel_V2.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>