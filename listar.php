<?php
// Conexão com o banco de dados
$conn = new mysqli("sql211.infinityfree.com", "if0_39598536", "040816bm", "if0_39598536_biblioteca");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Busca todos os livros
$result = $conn->query("SELECT * FROM cadastro ORDER BY id DESC");

// Cria um array para contar ISBNs repetidos
$contadorISBN = [];
$livros = [];

while ($row = $result->fetch_assoc()) {
    $isbnOriginal = $row['isbn'];
    $isbnLimpo = preg_replace('/[^0-9Xx]/', '', trim($isbnOriginal)); // Remove traços, espaços, etc.
    $contadorISBN[$isbnLimpo] = ($contadorISBN[$isbnLimpo] ?? 0) + 1;
    $row['isbn_limpo'] = $isbnLimpo;
    $livros[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Livros Cadastrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 20px;
      background-color: #f8f9fa;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    .repetido {
      background-color: #ffe5e5 !important;
    }
    .filter-btn {
      margin-bottom: 15px;
    }
    body { background-color: #f8f9fa; padding: 20px; }
    .card-summary { min-width: 200px; }
    .table-container { max-height: 400px; overflow-y: auto; }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="mb-3 text-center">📘 Livros Cadastrados</h2>

    <div class="text-center">
      <button class="btn btn-outline-danger filter-btn" onclick="filtrarRepetidos()">🔍 Mostrar apenas ISBNs Repetidos</button>
    </div>

    <div class="text-center mt-3">
      <a href="index.html" class="btn btn-secondary">🔙 Voltar ao Início</a>
    </div>

    <table class="table table-bordered table-striped" id="tabelaLivros">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>ISBN</th>
          <th>Título</th>
          <th>Autor</th>
          <th>Editora</th>
          <th>Ano</th>
          <th>Páginas</th>
          <th>Plateleira</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($livros as $row): 
          $isbnRepetido = $contadorISBN[$row['isbn_limpo']] > 1;
        ?>
          <tr class="<?= $isbnRepetido ? 'repetido' : '' ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['isbn']) ?></td>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= htmlspecialchars($row['autor']) ?></td>
            <td><?= htmlspecialchars($row['editora']) ?></td>
            <td><?= htmlspecialchars($row['ano']) ?></td>
            <td><?= htmlspecialchars($row['paginas']) ?></td>
            <td><?= htmlspecialchars($row['plateleira']) ?></td>
            <td>
              <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
              <a href="excluir.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este livro?')">🗑️ Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-center mt-3">
      <a href="index.html" class="btn btn-secondary">🔙 Voltar para Cadastro</a>
    </div>
  </div>

  <script>
    let mostrandoRepetidos = false;

    function filtrarRepetidos() {
      const linhas = document.querySelectorAll("#tabelaLivros tbody tr");
      mostrandoRepetidos = !mostrandoRepetidos;

      linhas.forEach(linha => {
        const ehRepetido = linha.classList.contains("repetido");
        linha.style.display = (mostrandoRepetidos && !ehRepetido) ? "none" : "";
      });

      document.querySelector(".filter-btn").textContent = mostrandoRepetidos 
        ? "🔁 Mostrar Todos os Livros" 
        : "🔍 Mostrar apenas ISBNs Repetidos";
    }
  </script>
</body>
</html>
