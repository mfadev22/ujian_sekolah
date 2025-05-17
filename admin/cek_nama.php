<?php
require 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['nama'])) {
    echo json_encode(['error' => 'Nama tidak disertakan']);
    exit;
}

// Terima nama base64 encoded dari URL
$encodedName = $_GET['nama'];
$name = base64_decode($encodedName);

$stmt = $conn->prepare("SELECT COUNT(*) FROM murid_ujian WHERE nama = ?
");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo json_encode(['exists' => $count > 0]);
?>
