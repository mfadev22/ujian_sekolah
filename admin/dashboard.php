<?php
session_start();
require 'config.php';

$waktu_ujian = ['menit' => 0, 'detik' => 0];
$result = $conn->query("SELECT * FROM pengaturan_waktu LIMIT 1");
if ($result && $result->num_rows > 0) {
    $waktu_ujian = $result->fetch_assoc();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Dashboard Admin</title>
	<script src="https://cdn.tailwindcss.com"></script>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen font-['Poppins']">
	<!-- Header -->
	<div class="bg-white shadow p-6 flex justify-between items-center">
		<h1 class="text-2xl font-bold">Dashboard Guru</h1>
		<a href="logout.php" class="text-red-600 hover:underline">Logout</a>
	</div>
	<!-- Main Menu -->
	<div class="max-w-4xl mx-auto mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
		<a href="list_soal.php" class="bg-blue-500 hover:bg-blue-600 text-white text-center p-6 rounded shadow-md">
			<h2 class="text-xl font-semibold">Input Soal</h2>
		</a>
		<!-- Ubah jadi tombol, bukan link -->
		<button onclick="openModal()" class="bg-green-500 hover:bg-green-600 text-white text-center p-6 rounded shadow-md">
			<h2 class="text-xl font-semibold">Atur Waktu</h2>
		</button>
		<a href="list_murid.php" class="bg-yellow-500 hover:bg-yellow-600 text-white text-center p-6 rounded shadow-md">
			<h2 class="text-xl font-semibold">List Murid</h2>
		</a>
	</div>
	<!-- Modal Atur Waktu -->
	<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
		<div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
			<!-- Waktu Sekarang -->
			<div class="mb-4">
				<div class="text-center text-2xl font-semibold text-blue-600"> ⏱️ Waktu Ujian yang dipilih Saat Ini: <span class="block mt-1 text-3xl font-bold text-gray-800"> <?= $waktu_ujian['menit'] ?> Menit <?= $waktu_ujian['detik'] ?> Detik </span>
				</div>
			</div>
			<h2 class="text-xl font-bold mb-4">Set Waktu Ujian</h2>
			<form action="simpan_waktu.php" method="POST" class="space-y-4">
				<div>
					<label class="block text-gray-700 font-medium">Menit</label>
					<select name="minutes" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"> <?php for ($i = 0; $i <= 120; $i++): ?> <option value="<?= $i ?>"><?= $i ?> menit</option> <?php endfor; ?> </select>
				</div>
				<div>
					<label class="block text-gray-700 font-medium">Detik</label>
					<select name="seconds" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"> <?php for ($i = 0; $i < 60; $i+=5): ?> <option value="<?= $i ?>"><?= $i ?> detik</option> <?php endfor; ?> </select>
				</div>
				<div class="flex justify-end space-x-2">
					<button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
					<button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
				</div>
			</form>
		</div>
	</div>
	<!-- Modal Logic -->
	<script>
	function openModal() {
		document.getElementById('modal').classList.remove('hidden');
	}

	function closeModal() {
		document.getElementById('modal').classList.add('hidden');
	}
	</script>
</body>

</html>