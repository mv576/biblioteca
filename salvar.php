<?php
// Mostra erros (debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco
$host = "sql211.infinityfree.com";
$usuario = "if0_39598536";
$senha = "040816bm";
$banco = "if0_39598536_biblioteca";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Erro na conexão com o banco: " . $conn->connect_error;
    exit;
}

// Recebe dados do formulário
$isbn       = $_POST['isbn'] ?? '';
$titulo     = $_POST['titulo'] ?? '';
$autor      = $_POST['autor'] ?? '';
$editora    = $_POST['editora'] ?? '';
$ano        = $_POST['ano'] ?? '';
$paginas    = $_POST['paginas'] ?? 0;
$plateleira = $_POST['plateleira'] ?? '';
$tombamento = $_POST['tombamento'] ?? '';
$colecao    = $_POST['colecao'] ?? '';

// Validação mínima
if (empty($isbn) || empty($titulo) || empty($autor)) {
    http_response_code(400);
    echo "Campos obrigatórios (ISBN, Título, Autor) não preenchidos!";
    exit;
}

// Insere no banco incluindo o campo 'colecao'
$sql = "INSERT INTO cadastro (isbn, titulo, autor, editora, ano, paginas, plateleira, tombamento, colecao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar a query: " . $conn->error;
    exit;
}

// 'ssssissss' → cada letra representa o tipo do campo (s = string, i = inteiro)
$stmt->bind_param("ssssissss", 
    $isbn, 
    $titulo, 
    $autor, 
    $editora, 
    $ano, 
    $paginas, 
    $plateleira, 
    $tombamento, 
    $colecao
);

if ($stmt->execute()) {
    echo "Livro cadastrado com sucesso!";
} else {
    http_response_code(500);
    echo "Erro ao salvar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
