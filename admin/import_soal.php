<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';
require '../vendor/autoload.php'; // Pastikan path ini sesuai lokasi composer autoload

use PhpOffice\PhpSpreadsheet\IOFactory;

$inserted = 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] === 0) {
        $fileTmp = $_FILES['file_excel']['tmp_name'];

        $spreadsheet = IOFactory::load($fileTmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Mulai dari baris kedua, asumsi baris pertama header
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $pertanyaan = trim($row[0] ?? '');
            $jawaban_a = trim($row[1] ?? '');
            $jawaban_b = trim($row[2] ?? '');
            $jawaban_c = trim($row[3] ?? '');
            $jawaban_d = trim($row[4] ?? '');
            $jawaban_benar = strtolower(trim($row[5] ?? ''));

            if ($pertanyaan && $jawaban_a && $jawaban_b && $jawaban_c && $jawaban_d && in_array($jawaban_benar, ['a','b','c','d'])) {
                $stmt = $conn->prepare("INSERT INTO soal (pertanyaan, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $pertanyaan, $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_benar);
                $stmt->execute();
                $stmt->close();
                $inserted++;
            } else {
                $errors[] = "Baris " . ($i + 1) . " data tidak valid.";
            }
        }
    } else {
        $errors[] = "File belum diupload atau terjadi error.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Import Soal dari Excel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-['Poppins']">
  <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-10">
    <h1 class="text-2xl font-bold mb-6">Import Soal dari Excel</h1>

    <a href="contoh_format.xlsx" class="inline-block mb-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
        Download Contoh Format Excel
    </a>

    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($inserted > 0): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        Berhasil mengimport <?= $inserted ?> soal.<br />
        Anda akan diarahkan ke <strong>Daftar Soal</strong> dalam 5 detik.<br />
        <a href="list_soal.php" class="underline text-blue-600">Klik di sini jika tidak diarahkan otomatis</a>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label class="block mb-2 font-semibold">Pilih file Excel (.xlsx)</label>
      <input type="file" name="file_excel" accept=".xlsx" required class="mb-4" />
      <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600">Import</button>
      <a href="list_soal.php" class="ml-4 text-gray-700 hover:underline">Batal</a>
    </form>
  </div>

  <?php if ($inserted > 0): ?>
  <script>
    setTimeout(() => {
      window.location.href = 'list_soal.php';
    }, 5000);
  </script>
  <?php endif; ?>
</body>
</html>
