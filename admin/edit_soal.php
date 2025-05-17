<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pertanyaan = trim($_POST['pertanyaan']);
    $a = trim($_POST['jawaban_a']);
    $b = trim($_POST['jawaban_b']);
    $c = trim($_POST['jawaban_c']);
    $d = trim($_POST['jawaban_d']);
    $benar = strtolower(trim($_POST['jawaban_benar']));

    if ($pertanyaan && $a && $b && $c && $d && in_array($benar, ['a','b','c','d'])) {
        $stmt = $conn->prepare("UPDATE soal SET pertanyaan=?, jawaban_a=?, jawaban_b=?, jawaban_c=?, jawaban_d=?, jawaban_benar=? WHERE id=?");
        $stmt->bind_param("ssssssi", $pertanyaan, $a, $b, $c, $d, $benar, $id);
        $stmt->execute();
    }
}

header("Location: list_soal.php");
exit;
?>
