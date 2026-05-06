<?php

declare(strict_types=1);

define('ROOT', dirname(__DIR__));

require_once ROOT . '/app/config/database.php';
require_once ROOT . '/app/config/status_gizi.php';

$result = mysqli_query($conn, "SELECT id, nama_bayi, jenis_kelamin, umur_bulan, tinggi_badan, berat_badan, lingkar_lengan, lingkar_kepala, status_gizi FROM balita ORDER BY id ASC");

if (!$result) {
    fwrite(STDERR, "Gagal membaca data balita: " . mysqli_error($conn) . PHP_EOL);
    exit(1);
}

$total = 0;
$updated = 0;
$unchanged = 0;
$skipped = [];

while ($row = mysqli_fetch_assoc($result)) {
    $total++;

    $lila = $row['lingkar_lengan'] !== null ? (float)$row['lingkar_lengan'] : null;
    $lika = $row['lingkar_kepala'] !== null ? (float)$row['lingkar_kepala'] : null;

    if ($lila === null || $lila <= 0 || $lika === null || $lika <= 0) {
        $skipped[] = [
            'id' => $row['id'],
            'nama_bayi' => $row['nama_bayi'],
            'alasan' => 'LILA/LIKA kosong atau tidak valid',
        ];
        continue;
    }

    $statusBaru = hitungStatusGizi(
        $row['jenis_kelamin'],
        (int)$row['umur_bulan'],
        (float)$row['tinggi_badan'],
        (float)$row['berat_badan'],
        $lila,
        $lika
    );

    if ($statusBaru === null) {
        $skipped[] = [
            'id' => $row['id'],
            'nama_bayi' => $row['nama_bayi'],
            'alasan' => 'Status gizi tidak dapat dihitung',
        ];
        continue;
    }

    if ($statusBaru === $row['status_gizi']) {
        $unchanged++;
        continue;
    }

    $statusEscaped = mysqli_real_escape_string($conn, $statusBaru);
    $id = (int)$row['id'];

    if (!mysqli_query($conn, "UPDATE balita SET status_gizi = '$statusEscaped' WHERE id = $id")) {
        fwrite(STDERR, "Gagal update balita ID $id: " . mysqli_error($conn) . PHP_EOL);
        exit(1);
    }

    $updated++;
}

echo "Rekap hitung ulang status gizi" . PHP_EOL;
echo "Total data     : $total" . PHP_EOL;
echo "Diperbarui     : $updated" . PHP_EOL;
echo "Tidak berubah  : $unchanged" . PHP_EOL;
echo "Dilewati       : " . count($skipped) . PHP_EOL;

if ($skipped !== []) {
    echo PHP_EOL . "Data yang dilewati:" . PHP_EOL;
    foreach ($skipped as $item) {
        echo "- ID {$item['id']} | {$item['nama_bayi']} | {$item['alasan']}" . PHP_EOL;
    }
}
