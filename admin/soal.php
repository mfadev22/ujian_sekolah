<?php
require 'config.php';

// Ambil data dari database
$result = $conn->query("SELECT * FROM soal");

$questions = [];

while ($row = $result->fetch_assoc()) {
    // Tentukan index jawaban benar (0-3)
    $jawabanBenarIndex = [
        'a' => 0,
        'b' => 1,
        'c' => 2,
        'd' => 3
    ][$row['jawaban_benar']];

    // Masukkan ke array
    $questions[] = [
        'question' => $row['pertanyaan'],
        'options' => [
            $row['jawaban_a'],
            $row['jawaban_b'],
            $row['jawaban_c'],
            $row['jawaban_d']
        ],
        'answer' => $jawabanBenarIndex
    ];
}

// Ubah ke JSON agar bisa dibaca oleh JavaScript
header('Content-Type: application/json');
echo json_encode($questions, JSON_UNESCAPED_UNICODE);
