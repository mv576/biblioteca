<?php
// Conexão com o banco de dados
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID do livro não fornecido.");
}

// Executa a exclusão
$stmt = $conn->prepare("DELETE FROM cadastro WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: listar.php");
exit;
?>
