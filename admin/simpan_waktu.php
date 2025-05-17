<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php'; // gunakan koneksi dari config.php

$minutes = isset($_POST['minutes']) ? (int)$_POST['minutes'] : 0;
$seconds = isset($_POST['seconds']) ? (int)$_POST['seconds'] : 0;

// Hanya 1 baris, jadi replace
$conn->query("DELETE FROM pengaturan_waktu");
$stmt = $conn->prepare("INSERT INTO pengaturan_waktu (menit, detik) VALUES (?, ?)");
$stmt->bind_param("ii", $minutes, $seconds);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit;
