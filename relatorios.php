<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o banco
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Contadores para os cards
$res = $conn->query("SELECT COUNT(*) AS total FROM cadastro");
$totalLivros = $res ? $res->fetch_assoc()['total'] : 0;

$res = $conn->query("SELECT COUNT(DISTINCT aluno_id) AS total FROM emprestimos");
$totalAlunos = $res ? $res->fetch_assoc()['total'] : 0;

$res = $conn->query("SELECT COUNT(*) AS total FROM emprestimos WHERE data_devolucao IS NULL");
$emprestimosAtivos = $res ? $res->fetch_assoc()['total'] : 0;

$res = $conn->query("SELECT COUNT(*) AS total FROM emprestimos WHERE data_devolucao IS NOT NULL");
$emprestimosConcluidos = $res ? $res->fetch_assoc()['total'] : 0;

// Filtros
$filtroAluno = $_GET['aluno'] ?? '';
$filtroInicio = $_GET['inicio'] ?? '';
$filtroFim = $_GET['fim'] ?? '';

// Consulta histórico (sem tabela alunos, usa nome_usuario direto)
$sql = "SELECT e.id, c.titulo, c.isbn, e.aluno_id, e.data_emprestimo, e.data_devolucao 
        FROM emprestimos e
        JOIN cadastro c ON e.livro_id = c.id
        WHERE 1=1";

if ($filtroAluno) $sql .= " AND e.aluno_id LIKE '%".$conn->real_escape_string($filtroAluno)."%'";
if ($filtroInicio) $sql .= " AND e.data_emprestimo >= '".$conn->real_escape_string($filtroInicio)."'";
if ($filtroFim) $sql .= " AND e.data_emprestimo <= '".$conn->real_escape_string($filtroFim)."'";

$sql .= " ORDER BY e.data_emprestimo DESC";
$historico = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatórios da Biblioteca</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding: 20px; }
    .card-summary { min-width: 200px; }
    .table-container { max-height: 400px; overflow-y: auto; }
  </style>
</head>
<body>
  <div class="container bg-white p-4 rounded shadow">
    <h2 class="text-center mb-4">📊 Relatórios da Biblioteca</h2>

    <!-- Cards resumo -->
    <div class="row mb-4 text-center">
      <div class="col-md-3">
        <div class="card card-summary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📚 Livros</h5>
            <p class="display-6"><?= $totalLivros ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-summary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">👩‍🎓 Usuários</h5>
            <p class="display-6"><?= $totalAlunos ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-summary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">📖 Ativos</h5>
            <p class="display-6"><?= $emprestimosAtivos ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-summary shadow-sm">
          <div class="card-body">
            <h5 class="card-title">✅ Concluídos</h5>
            <p class="display-6"><?= $emprestimosConcluidos ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <form class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="aluno" class="form-control" placeholder="Nome do Usuário" value="<?= htmlspecialchars($filtroAluno) ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="inicio" class="form-control" value="<?= htmlspecialchars($filtroInicio) ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="fim" class="form-control" value="<?= htmlspecialchars($filtroFim) ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Filtrar</button>
      </div>
    </form>

    <!-- Tabela -->
    <h4 class="mb-3">📖 Histórico de Empréstimos</h4>
    <div class="table-container">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Livro</th>
            <th>ISBN</th>
            <th>Usuário</th>
            <th>Data Empréstimo</th>
            <th>Data Devolução</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $historico->fetch_assoc()): ?>
          <tr class="<?= $row['data_devolucao'] ? '' : 'table-warning' ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= htmlspecialchars($row['isbn']) ?></td>
            <td><?= htmlspecialchars($row['aluno_id']) ?></td>
            <td><?= date("d/m/Y", strtotime($row['data_emprestimo'])) ?></td>
            <td><?= $row['data_devolucao'] ? date("d/m/Y", strtotime($row['data_devolucao'])) : '⏳ Em aberto' ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-3">
      <a href="index.html" class="btn btn-secondary">🔙 Voltar ao Início</a>
    </div>
  </div>
</body>
</html>
