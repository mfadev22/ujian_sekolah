<?php
require 'config.php';

// var_dump($_POST);

$nama = $_POST['nama'];
$nilai = $_POST['nilai'];
$jawaban = $_POST['jawaban'];
$waktu_submit = date("Y-m-d H:i:s");

// Gunakan $conn karena itu yang didefinisikan di config.php
$stmt = $conn->prepare("INSERT INTO murid_ujian (nama, nilai, jawaban, waktu_submit) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdss", $nama, $nilai, $jawaban, $waktu_submit);

if ($stmt->execute()) {
    echo "Data berhasil disimpan";
} else {
    echo "Gagal menyimpan: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
