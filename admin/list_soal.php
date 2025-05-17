<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

$result = $conn->query("SELECT * FROM soal ORDER BY id ASC");
$soalList = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8" />
	<title>List Soal</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
	<script>
	function confirmDelete(id) {
		if(confirm('Yakin ingin menghapus soal ini?')) {
			window.location.href = 'hapus_soal.php?id=' + id;
		}
	}

	function openEditModal(soal) {
		document.getElementById('editForm').action = 'edit_soal.php?id=' + soal.id;
		document.getElementById('e_pertanyaan').value = soal.pertanyaan;
		document.getElementById('e_jawaban_a').value = soal.jawaban_a;
		document.getElementById('e_jawaban_b').value = soal.jawaban_b;
		document.getElementById('e_jawaban_c').value = soal.jawaban_c;
		document.getElementById('e_jawaban_d').value = soal.jawaban_d;
		document.getElementById('e_jawaban_benar').value = soal.jawaban_benar;
		document.getElementById('editModal').classList.remove('hidden');
	}

	function closeEditModal() {
		document.getElementById('editModal').classList.add('hidden');
	}
	</script>
</head>

<body class="bg-gray-100 min-h-screen font-['Poppins']">
	<div class="max-w-6xl mx-auto p-6">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-bold">Daftar Soal</h1>
			<div>
				<a href="dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-2">Kembali</a>
				<a href="tambah_soal.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Soal</a>
				<a href="import_soal.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Import Excel</a>
			</div>
		</div> <?php if (count($soalList) === 0): ?> <p class="text-gray-600">Belum ada soal yang diinput.</p> <?php else: ?> <div class="bg-white shadow rounded-lg overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200 text-sm">
				<thead class="bg-gray-200 text-gray-700">
					<tr>
						<th class="py-3 px-4 text-left">No</th>
						<th class="py-3 px-4 text-left">Pertanyaan</th>
						<th class="py-3 px-4 text-left">A</th>
						<th class="py-3 px-4 text-left">B</th>
						<th class="py-3 px-4 text-left">C</th>
						<th class="py-3 px-4 text-left">D</th>
						<th class="py-3 px-4 text-left">Benar</th>
						<th class="py-3 px-4 text-left">Aksi</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-100"> <?php foreach ($soalList as $index => $soal): ?> <tr>
						<td class="py-3 px-4 align-top"><?= $index + 1 ?></td>
						<td class="py-3 px-4 align-top"><?= htmlspecialchars($soal['pertanyaan']) ?></td>
						<td class="py-3 px-4 align-top"><?= htmlspecialchars($soal['jawaban_a']) ?></td>
						<td class="py-3 px-4 align-top"><?= htmlspecialchars($soal['jawaban_b']) ?></td>
						<td class="py-3 px-4 align-top"><?= htmlspecialchars($soal['jawaban_c']) ?></td>
						<td class="py-3 px-4 align-top"><?= htmlspecialchars($soal['jawaban_d']) ?></td>
						<td class="py-3 px-4 align-top font-bold text-green-700 uppercase"><?= htmlspecialchars($soal['jawaban_benar']) ?></td>
						<td class="py-3 px-4 align-top space-x-2 flex">
							<!-- Tombol Edit -->
							<button onclick='openEditModal(<?= json_encode($soal) ?>)' class="text-blue-600 hover:text-blue-800" title="Edit">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M13.5 6.5L4 16v4h4L19.5 8.5a2.121 2.121 0 00-3-3z" />
								</svg>
							</button>
							<!-- Tombol Hapus -->
							<button onclick="confirmDelete(<?= $soal['id'] ?>)" class="text-red-600 hover:text-red-800" title="Hapus">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</td>
					</tr> <?php endforeach; ?> </tbody>
			</table>
		</div> <?php endif; ?>
	</div>
	<!-- Modal Edit -->
	<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
		<div class="bg-white p-6 rounded-lg w-full max-w-xl shadow-lg">
			<h2 class="text-xl font-bold mb-4">Edit Soal</h2>
			<form id="editForm" method="POST">
				<div class="mb-3">
					<label class="block mb-1">Pertanyaan</label>
					<textarea id="e_pertanyaan" name="pertanyaan" class="w-full border rounded p-2" required></textarea>
				</div>
				<div class="grid grid-cols-2 gap-4">
					<div><label>A</label><input type="text" id="e_jawaban_a" name="jawaban_a" class="w-full border rounded p-2" required></div>
					<div><label>B</label><input type="text" id="e_jawaban_b" name="jawaban_b" class="w-full border rounded p-2" required></div>
					<div><label>C</label><input type="text" id="e_jawaban_c" name="jawaban_c" class="w-full border rounded p-2" required></div>
					<div><label>D</label><input type="text" id="e_jawaban_d" name="jawaban_d" class="w-full border rounded p-2" required></div>
				</div>
				<div class="mt-3">
					<label class="block mb-1">Jawaban Benar</label>
					<select id="e_jawaban_benar" name="jawaban_benar" class="w-full border rounded p-2" required>
						<option value="">Pilih...</option>
						<option value="a">A</option>
						<option value="b">B</option>
						<option value="c">C</option>
						<option value="d">D</option>
					</select>
				</div>
				<div class="flex justify-end mt-6 space-x-2">
					<button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
					<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
				</div>
			</form>
		</div>
	</div>
	<!-- Modal Hapus -->
	<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
		<div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
			<h2 class="text-xl font-semibold text-gray-800 mb-4">Hapus Soal</h2>
			<p class="text-gray-600 mb-6">Yakin ingin menghapus soal ini?</p>
			<div class="flex justify-end space-x-3">
				<button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"> Batal </button>
				<form id="deleteForm" method="POST" action="hapus_soal.php">
					<input type="hidden" name="id" id="deleteId" />
					<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"> Hapus </button>
				</form>
			</div>
		</div>
	</div>
	<script>
	function confirmDelete(id) {
		document.getElementById('deleteId').value = id;
		document.getElementById('deleteModal').classList.remove('hidden');
	}

	function closeDeleteModal() {
		document.getElementById('deleteModal').classList.add('hidden');
	}
	</script>
</body>

</html>