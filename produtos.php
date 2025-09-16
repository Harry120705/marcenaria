<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$result = $conn->query('SELECT id, nome, preco, quantidade FROM produtos');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="banner">Produtos</div>
    <div class="container">
        <div class="actions" style="justify-content: flex-end; margin-bottom: 18px;">
            <a href="produto.php">Novo Produto</a>
        </div>
        <div class="produtos-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="produto-card">
                <div class="produto-info">
                    <h3><?= htmlspecialchars($row['nome']) ?></h3>
                    <p><strong>ID:</strong> <?= $row['id'] ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($row['preco'], 2, ',', '.') ?></p>
                    <p><strong>Quantidade:</strong> <?= $row['quantidade'] ?></p>
                </div>
                <div class="actions">
                    <a href="produto.php?id=<?= $row['id'] ?>">Ver/Editar</a>
                    <a href="produtos.php?del=<?= $row['id'] ?>" onclick="return confirm('Excluir produto?')">Excluir</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="actions" style="margin-top: 32px;">
            <a href="index.php">Voltar</a>
        </div>
    </div>
</body>
</html>
<?php
// Excluir produto
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM produtos WHERE id = $id");
    header('Location: produtos.php');
    exit;
}
?>
