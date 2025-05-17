<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

// Ambil data murid
$result = $conn->query("SELECT * FROM murid_ujian ORDER BY waktu_submit DESC");
$muridList = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>List Murid</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-['Poppins']">
  <div class="max-w-5xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Daftar Murid yang Telah Mengerjakan</h1>
      <a href="dashboard.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kembali</a>
    </div>

    <?php if (count($muridList) === 0): ?>
      <p class="text-gray-600">Belum ada murid yang mengerjakan ujian.</p>
    <?php else: ?>
      <div class="mb-4">
        <input
          type="text"
          id="searchInput"
          placeholder="Cari nama murid..."
          class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          onkeyup="filterTable()"
        />
      </div>

      <div class="overflow-x-auto">
        <table id="muridTable" class="w-full bg-white shadow rounded-lg overflow-hidden">
          <thead class="bg-gray-200 text-gray-700">
            <tr>
              <th class="py-3 px-4 text-left">Nama</th>
              <th class="py-3 px-4 text-left">Nilai</th>
              <th class="py-3 px-4 text-left">Waktu Submit</th>
              <th class="py-3 px-4 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($muridList as $murid): ?>
              <tr class="border-t">
                <td class="py-3 px-4"><?= htmlspecialchars($murid['nama']) ?></td>
                <td class="py-3 px-4"><?= $murid['nilai'] ?></td>
                <td class="py-3 px-4"><?= $murid['waktu_submit'] ?></td>
                <td class="py-3 px-4 space-x-2">
                  <a href="lihat_hasil.php?id=<?= $murid['id'] ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Lihat Hasil</a>
                  <a href="hapus_murid.php?id=<?= $murid['id'] ?>" onclick="return confirm('Yakin ingin menghapus murid ini?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function filterTable() {
      const input = document.getElementById("searchInput");
      const filter = input.value.toLowerCase();
      const table = document.getElementById("muridTable");
      const trs = table.tBodies[0].getElementsByTagName("tr");

      for (let i = 0; i < trs.length; i++) {
        const td = trs[i].getElementsByTagName("td")[0]; // kolom nama
        if (td) {
          const txtValue = td.textContent || td.innerText;
          if (txtValue.toLowerCase().indexOf(filter) > -1) {
            trs[i].style.display = "";
          } else {
            trs[i].style.display = "none";
          }
        }
      }
    }
  </script>
</body>
</html>
