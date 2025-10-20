<?php
// Exibe erros (útil para debug; remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco de dados
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$id) {
    die("ID do livro não fornecido.");
}

// Se o formulário foi submetido, faz o update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn       = $_POST['isbn'] ?? '';
    $titulo     = $_POST['titulo'] ?? '';
    $autor      = $_POST['autor'] ?? '';
    $editora    = $_POST['editora'] ?? '';
    $ano        = $_POST['ano'] ?? '';
    $paginas    = isset($_POST['paginas']) ? intval($_POST['paginas']) : 0;
    $plateleira = $_POST['plateleira'] ?? '';
    $tombamento = $_POST['tombamento'] ?? '';

    // Validação mínima
    if (empty($isbn) || empty($titulo) || empty($autor)) {
        $erro = "Campos obrigatórios (ISBN, Título, Autor) não podem estar vazios.";
    } else {
        $stmt = $conn->prepare("UPDATE cadastro SET isbn=?, titulo=?, autor=?, editora=?, ano=?, paginas=?, plateleira=?, tombamento=? WHERE id=?");
        if (!$stmt) {
            die("Erro ao preparar statement: " . $conn->error);
        }
        // isbn, titulo, autor, editora, ano (strings), paginas (int), plateleira, tombamento (strings), id (int)
        $stmt->bind_param("sssssissi", $isbn, $titulo, $autor, $editora, $ano, $paginas, $plateleira, $tombamento, $id);
        if ($stmt->execute()) {
            header("Location: listar.php?edit=success");
            exit;
        } else {
            $erro = "Erro ao salvar as alterações: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Busca os dados atuais para preencher o formulário
$stmt = $conn->prepare("SELECT * FROM cadastro WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$livro = $result->fetch_assoc();
$stmt->close();

if (!$livro) {
    die("Livro não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Livro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container bg-white p-4 rounded shadow" style="max-width: 800px; margin: auto;">
    <h2 class="mb-4 text-center">✏️ Editar Livro</h2>

    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="row">
        <div class="mb-3 col-md-6">
          <label class="form-label">ISBN</label>
          <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($livro['isbn']) ?>" required>
        </div>
        <div class="mb-3 col-md-6">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($livro['titulo']) ?>" required>
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-6">
          <label class="form-label">Autor</label>
          <input type="text" name="autor" class="form-control" value="<?= htmlspecialchars($livro['autor']) ?>">
        </div>
        <div class="mb-3 col-md-6">
          <label class="form-label">Editora</label>
          <input type="text" name="editora" class="form-control" value="<?= htmlspecialchars($livro['editora']) ?>">
        </div>
      </div>

      <div class="row">
        <div class="mb-3 col-md-4">
          <label class="form-label">Ano</label>
          <input type="text" name="ano" class="form-control" value="<?= htmlspecialchars($livro['ano']) ?>">
        </div>
        <div class="mb-3 col-md-4">
          <label class="form-label">Páginas</label>
          <input type="number" name="paginas" class="form-control" value="<?= htmlspecialchars($livro['paginas']) ?>">
        </div>
        <div class="mb-3 col-md-4">
          <label class="form-label">Plateleira</label>
          <input type="text" name="plateleira" class="form-control" value="<?= htmlspecialchars($livro['plateleira'] ?? '') ?>">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Tombamento</label>
        <input type="text" name="tombamento" class="form-control" value="<?= htmlspecialchars($livro['tombamento'] ?? '') ?>">
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</body>
</html>
