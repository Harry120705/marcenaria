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
    <h2>Produtos</h2>
    <table border="1">
        <tr><th>ID</th><th>Nome</th><th>Preço</th><th>Quantidade</th><th>Ações</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nome'] ?></td>
            <td>R$ <?= number_format($row['preco'], 2, ',', '.') ?></td>
            <td><?= $row['quantidade'] ?></td>
            <td>
                <a href="produto.php?id=<?= $row['id'] ?>">Ver</a> |
                <a href="produtos.php?del=<?= $row['id'] ?>" onclick="return confirm('Excluir produto?')">Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="produto.php">Novo Produto</a>
    <br><a href="index.php">Voltar</a>
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
