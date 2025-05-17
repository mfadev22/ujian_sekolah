<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: list_murid.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM murid_ujian WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$murid = $result->fetch_assoc();
$stmt->close();

if (!$murid) {
    echo "Murid tidak ditemukan.";
    exit;
}

$jawaban = explode(",", $murid['jawaban']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Ujian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-['Poppins']">
  <div class="max-w-xl mx-auto p-6 bg-white rounded shadow mt-10">
    <h2 class="text-2xl font-bold mb-4">Hasil Ujian: <?= htmlspecialchars($murid['nama']) ?></h2>
    <p class="mb-2 text-gray-700"><strong>Nilai:</strong> <?= $murid['nilai'] ?></p>
    <p class="mb-4 text-gray-700"><strong>Waktu Submit:</strong> <?= $murid['waktu_submit'] ?></p>

    <h3 class="text-lg font-semibold mb-2">Jawaban:</h3>
    <ul class="list-disc list-inside text-gray-800">
      <?= $murid['jawaban'] ?>
    </ul>

    <div class="mt-6">
      <a href="list_murid.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kembali</a>
    </div>
  </div>
</body>
</html>
