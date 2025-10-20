<?php
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");

$serie = $_GET['serie'] ?? '';

$stmt = $conn->prepare("SELECT id, nome FROM alunos WHERE serie=? ORDER BY nome");
$stmt->bind_param("s", $serie);
$stmt->execute();
$result = $stmt->get_result();

$alunos = [];
while ($row = $result->fetch_assoc()) {
    $alunos[] = $row;
}

echo json_encode($alunos);
