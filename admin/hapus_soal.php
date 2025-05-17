<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM soal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: list_soal.php");
exit;
