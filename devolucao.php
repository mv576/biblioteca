<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Registrar devolução
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['emprestimos'])) {
    $emprestimos = $_POST['emprestimos']; // array de ids de empréstimo
    $data_devolucao = date("Y-m-d");

    $stmt = $conn->prepare("UPDATE emprestimos SET data_devolucao=? WHERE id=?");
    foreach ($emprestimos as $id) {
        $stmt->bind_param("si", $data_devolucao, $id);
        $stmt->execute();
    }

    echo "<script>alert('✅ Devolução registrada com sucesso!'); window.location='devolucao.php';</script>";
    exit;
}

// Buscar todos empréstimos em aberto
$sql = "SELECT e.id, e.data_emprestimo, c.titulo, c.tombamento, a.nome AS aluno, a.serie
        FROM emprestimos e
        JOIN cadastro c ON e.livro_id = c.id
        JOIN alunos a ON e.aluno_id = a.id
        WHERE e.data_devolucao IS NULL
        ORDER BY e.data_emprestimo ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>📚 Devolução de Livros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container bg-white p-4 rounded shadow">
    <h2 class="mb-4 text-center">📖 Devolução de Livros</h2>

    <?php if ($result->num_rows > 0): ?>
      <form method="post">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Selecionar</th>
              <th>Livro</th>
              <th>Tombamento</th>
              <th>Aluno</th>
              <th>Série</th>
              <th>Data Empréstimo</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" name="emprestimos[]" value="<?= $row['id'] ?>"></td>
                <td><?= htmlspecialchars($row['titulo']) ?></td>
                <td><?= htmlspecialchars($row['tombamento']) ?></td>
                <td><?= htmlspecialchars($row['aluno']) ?></td>
                <td><?= htmlspecialchars($row['serie']) ?></td>
                <td><?= date("d/m/Y", strtotime($row['data_emprestimo'])) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div class="text-center">
          <button type="submit" class="btn btn-success">📦 Registrar Devolução</button>
        </div>
      </form>
    <?php else: ?>
      <div class="alert alert-info">✅ Não há livros em aberto para devolução.</div>
    <?php endif; ?>
  </div>
</body>
</html>
