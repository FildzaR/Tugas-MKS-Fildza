<?php
// Load file JSON dari folder private (Tidak bisa diakses via web)
$json_file = __DIR__ . '/../private/data_sekolah.json';
$json_data = file_get_contents($json_file);

if ($json_data === false) {
    die("Error membaca data.");
}

$data = json_decode($json_data, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON tidak valid: " . json_last_error_msg());
}

// Kelompokkan data berdasarkan sekolah
$grouped_data = [];
foreach ($data as $item) {
    $school = $item['sekolah'];

    if (!isset($grouped_data[$school])) {
        $grouped_data[$school] = [
            'pdf_file' => $item['pdf_file'],
            'pendamping' => []
        ];
    }

    foreach ($item['pendamping'] as $i => $nama) {
        $grouped_data[$school]['pendamping'][] = [
            'name' => $nama,
            // Kode verifikasi TIDAK diberikan ke HTML !!!
            'id'   => $i // hanya index pendamping
        ];
    }
}

ksort($grouped_data);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Peserta Bebras 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Daftar Peserta Bebras Challenge 2025</h1>

<div class="school-list">

<?php foreach ($grouped_data as $school => $info): ?>
    <div class="school-item">
        <h3><?= htmlspecialchars($school) ?></h3>
        <ul>

        <?php foreach ($info['pendamping'] as $p): ?>
            <li>
                <strong>Pendamping: </strong><?= htmlspecialchars($p['name']) ?>

                <!-- Tidak ada expectedCode di JS !!! -->
                <button onclick="openModal(
                    '<?= $info['pdf_file'] ?>',
                    '<?= $p['id'] ?>',
                    '<?= htmlspecialchars($p['name']) ?>'
                )">Download PDF</button>
            </li>
        <?php endforeach; ?>

        </ul>
    </div>
<?php endforeach; ?>

</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal()" class="close">&times;</span>

        <h3>Verifikasi Unduhan</h3>
        <p id="pendampingDisplay"></p>

        <form id="verificationForm" method="POST" action="download.php">
            <input type="hidden" id="pdfFile" name="pdf_file">
            <input type="hidden" id="pendampingId" name="pendamping_id">

            <label>Kode Verifikasi (4 digit):</label>
            <input type="text" name="code" maxlength="4" required>

            <button type="submit">Verifikasi</button>
        </form>

    </div>
</div>

<script>
function openModal(pdfFile, pendampingId, name) {
    document.getElementById("pdfFile").value = pdfFile;
    document.getElementById("pendampingId").value = pendampingId;
    document.getElementById("pendampingDisplay").textContent = "Pendamping: " + name;
    document.getElementById("myModal").style.display = "block";
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}
</script>

</body>
</html>
