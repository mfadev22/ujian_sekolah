<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan = $_POST['pertanyaan'] ?? '';
    $jawaban_a = $_POST['jawaban_a'] ?? '';
    $jawaban_b = $_POST['jawaban_b'] ?? '';
    $jawaban_c = $_POST['jawaban_c'] ?? '';
    $jawaban_d = $_POST['jawaban_d'] ?? '';
    $jawaban_benar = $_POST['jawaban_benar'] ?? '';

    // Validasi sederhana
    if ($pertanyaan && $jawaban_a && $jawaban_b && $jawaban_c && $jawaban_d && in_array($jawaban_benar, ['a','b','c','d'])) {
        $stmt = $conn->prepare("INSERT INTO soal (pertanyaan, jawaban_a, jawaban_b, jawaban_c, jawaban_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $pertanyaan, $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_benar);
        $stmt->execute();
        $stmt->close();

        header("Location: list_soal.php?msg=success");
        exit;
    } else {
        $error = "Semua field harus diisi dengan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Tambah Soal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-['Poppins']">
  <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-10">
    <h1 class="text-2xl font-bold mb-6">Tambah Soal Baru</h1>

    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label class="block mb-2 font-semibold">Pertanyaan</label>
      <textarea name="pertanyaan" class="w-full p-2 border rounded mb-4" rows="3" required><?= htmlspecialchars($_POST['pertanyaan'] ?? '') ?></textarea>

      <label class="block mb-2 font-semibold">Jawaban A</label>
      <input type="text" name="jawaban_a" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($_POST['jawaban_a'] ?? '') ?>" required />

      <label class="block mb-2 font-semibold">Jawaban B</label>
      <input type="text" name="jawaban_b" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($_POST['jawaban_b'] ?? '') ?>" required />

      <label class="block mb-2 font-semibold">Jawaban C</label>
      <input type="text" name="jawaban_c" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($_POST['jawaban_c'] ?? '') ?>" required />

      <label class="block mb-2 font-semibold">Jawaban D</label>
      <input type="text" name="jawaban_d" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($_POST['jawaban_d'] ?? '') ?>" required />

      <label class="block mb-2 font-semibold">Jawaban Benar</label>
      <select name="jawaban_benar" class="w-full p-2 border rounded mb-6" required>
        <option value="">-- Pilih Jawaban Benar --</option>
        <option value="a" <?= (($_POST['jawaban_benar'] ?? '') === 'a') ? 'selected' : '' ?>>A</option>
        <option value="b" <?= (($_POST['jawaban_benar'] ?? '') === 'b') ? 'selected' : '' ?>>B</option>
        <option value="c" <?= (($_POST['jawaban_benar'] ?? '') === 'c') ? 'selected' : '' ?>>C</option>
        <option value="d" <?= (($_POST['jawaban_benar'] ?? '') === 'd') ? 'selected' : '' ?>>D</option>
      </select>

      <button type="submit" class="bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600">Simpan Soal</button>
      <a href="list_soal.php" class="ml-4 text-gray-700 hover:underline">Batal</a>
    </form>
  </div>
</body>
</html>
