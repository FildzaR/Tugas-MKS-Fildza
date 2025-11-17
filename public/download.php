<?php

// Jalankan hanya via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode tidak valid.");
}

$pdf_file = $_POST['pdf_file'] ?? '';
$pendamping_id = intval($_POST['pendamping_id'] ?? 0);
$code_input = $_POST['code'] ?? '';

// Load data
$json = json_decode(file_get_contents(__DIR__ . '/../private/data_sekolah.json'), true);

// Cari pendamping dan kode verifikasi
$expected_code = null;
foreach ($json as $item) {
    if ($item['pdf_file'] === $pdf_file) {
        if (isset($item['verification_codes'][$pendamping_id])) {
            $expected_code = $item['verification_codes'][$pendamping_id];
        }
    }
}

if ($expected_code === null) {
    die("Data tidak valid.");
}

// Cek kode
if ($code_input !== $expected_code) {
    die("<h2>Kode verifikasi salah!</h2>");
}

// Lokasi PDF (tidak bisa diakses langsung via URL)
$file_path = __DIR__ . '/../private/pdf_files/' . $pdf_file;

if (!file_exists($file_path)) {
    die("File tidak ditemukan.");
}

// Kirim file ke browser
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$pdf_file\"");
readfile($file_path);
exit;

