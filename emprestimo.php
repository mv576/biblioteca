<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar séries distintas
$series = $conn->query("SELECT DISTINCT serie FROM alunos ORDER BY serie");

// Buscar coleções distintas
$colecoes = $conn->query("SELECT DISTINCT colecao FROM cadastro WHERE colecao IS NOT NULL AND colecao <> '' ORDER BY colecao");

// Buscar livros pelo ISBN se pesquisado
$livrosEncontrados = [];
if (isset($_GET['pesquisar_isbn'])) {
    $isbn = $_GET['pesquisar_isbn'];
    $stmt = $conn->prepare("SELECT * FROM cadastro WHERE isbn=?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $livrosEncontrados[] = $row;
    }
}

// Buscar livros de uma coleção se selecionada
$livrosColecao = [];
if (isset($_GET['colecao'])) {
    $colecaoEscolhida = $_GET['colecao'];
    $stmt = $conn->prepare("SELECT * FROM cadastro WHERE colecao=?");
    $stmt->bind_param("s", $colecaoEscolhida);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $livrosColecao[] = $row;
    }
}

// Registrar empréstimo (individual ou coleção)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $livros = $_POST['livros'] ?? []; // array de livros selecionados
    $data_emprestimo = date("Y-m-d");

    if (!empty($livros)) {
        $stmt = $conn->prepare("INSERT INTO emprestimos (aluno_id, livro_id, tombamento, data_emprestimo) VALUES (?, ?, ?, ?)");
        foreach ($livros as $livro) {
            list($livro_id, $tombamento) = explode("|", $livro);
            $stmt->bind_param("iiss", $aluno_id, $livro_id, $tombamento, $data_emprestimo);
            $stmt->execute();
        }
    }

    echo "<script>alert('✅ Empréstimo registrado com sucesso!'); window.location='emprestimo.php';</script>";
    exit;
}
?>

<style>

body { background-color: #f8f9fa; padding: 20px; }
    .card-summary { min-width: 200px; }
    .table-container { max-height: 400px; overflow-y: auto; }

    </style>

    
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>📚 Empréstimo de Livro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function carregarAlunos(serie, selectId) {
      fetch("get_alunos.php?serie=" + serie)
        .then(res => res.json())
        .then(data => {
          let select = document.getElementById(selectId);
          select.innerHTML = "";
          if (data.length === 0) {
            let opt = document.createElement("option");
            opt.text = "Nenhum aluno encontrado";
            select.appendChild(opt);
          } else {
            data.forEach(aluno => {
              let opt = document.createElement("option");
              opt.value = aluno.id;
              opt.text = aluno.nome;
              select.appendChild(opt);
            });
          }
        });
    }
  </script>
</head>
<body class="bg-light p-4">
  <div class="container bg-white p-4 rounded shadow">
    <h2 class="mb-4 text-center">📖 Registrar Empréstimo</h2>

    <!-- Pesquisa por ISBN -->
    <form method="get" class="mb-4 d-flex">
      <input type="text" name="pesquisar_isbn" class="form-control me-2" placeholder="Digite o ISBN do livro" required>
      <button type="submit" class="btn btn-info">🔍 Buscar</button>
    </form>

    <!-- Seleção de coleção -->
    <form method="get" class="mb-4 d-flex">
      <select name="colecao" class="form-select me-2" required>
        <option value="">Selecione uma coleção</option>
        <?php while ($c = $colecoes->fetch_assoc()): ?>
          <option value="<?= $c['colecao'] ?>" <?= (isset($colecaoEscolhida) && $colecaoEscolhida == $c['colecao']) ? 'selected' : '' ?>>
            <?= $c['colecao'] ?>
          </option>
        <?php endwhile; ?>
      </select>
      <button type="submit" class="btn btn-success">📚 Ver Livros da Coleção</button>
    </form>

    <!-- Resultado ISBN -->
<?php if (!empty($livrosEncontrados)): ?>
  <?php foreach ($livrosEncontrados as $index => $livro): ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($livro['titulo']) ?></h5>
        <p class="card-text">
          <strong>Editora:</strong> <?= htmlspecialchars($livro['editora']) ?> <br>
          <strong>Ano:</strong> <?= htmlspecialchars($livro['ano']) ?> <br>
          <strong>Páginas:</strong> <?= htmlspecialchars($livro['paginas']) ?> <br>
          <strong>Tombamento:</strong> <?= htmlspecialchars($livro['tombamento']) ?>
        </p>

        <!-- Empréstimo de livro único -->
        <form method="post">
          <input type="hidden" name="livros[]" value="<?= $livro['id'] ?>|<?= $livro['tombamento'] ?>">

          <div class="mb-3">
            <label>Série</label>
            <select name="serie" class="form-select" 
                    onchange="carregarAlunos(this.value, 'aluno<?= $index ?>')" required>
              <option value="">Selecione a série ou Professor</option>
              <?php $series->data_seek(0); ?>
              <?php while ($s = $series->fetch_assoc()): ?>
                <option value="<?= $s['serie'] ?>"><?= $s['serie'] ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="text-center mt-3">
      <a href="index.html" class="btn btn-secondary">🔙 Voltar ao Início</a>
    </div>

          <div class="mb-3">
            <label>Aluno</label>
            <select id="aluno<?= $index ?>" name="aluno_id" class="form-select" required>
              <option value="">Selecione a série primeiro ou Professor</option>
            </select>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary">📖 Registrar Empréstimo</button>
          </div>
          
        </form>
        
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

    <!-- Resultado coleção -->
<?php if (!empty($livrosColecao)): ?>
  <div class="card p-3 shadow-sm">
    <h5>📚 Livros da Coleção: <?= htmlspecialchars($colecaoEscolhida) ?></h5>
    <form method="post">
      <ul class="list-group mb-3">
        <?php foreach ($livrosColecao as $livro): ?>
          <li class="list-group-item">
            <input type="checkbox" name="livros[]" value="<?= $livro['id'] ?>|<?= $livro['tombamento'] ?>">
            <?= htmlspecialchars($livro['titulo']) ?> - <strong>Tombamento:</strong> <?= htmlspecialchars($livro['tombamento']) ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="mb-3">
        <label>Série</label>
        <select name="serie" class="form-select" onchange="carregarAlunos(this.value, 'alunoColecao')" required>
          <option value="">Selecione a série</option>
          <?php $series->data_seek(0); ?>
          <?php while ($s = $series->fetch_assoc()): ?>
            <option value="<?= $s['serie'] ?>"><?= $s['serie'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Aluno</label>
        <select id="alunoColecao" name="aluno_id" class="form-select" required>
          <option value="">Selecione a série primeiro</option>
        </select>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-success">📖 Registrar Empréstimo da Coleção</button>
      </div>
    </form>
  </div>
<?php endif; ?>

  </div>
</body>
</html>
