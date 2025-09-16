<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare('SELECT id, nome, descricao, preco, quantidade FROM produtos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $nome, $descricao, $preco, $quantidade);
    $stmt->fetch();
    $stmt->close();
} else {
    $id = '';
    $nome = '';
    $descricao = '';
    $preco = '';
    $quantidade = '';
}
// Salvar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoNome = $_POST['nome'] ?? '';
    $novaDescricao = $_POST['descricao'] ?? '';
    $novoPreco = $_POST['preco'] ?? '';
    $novaQuantidade = $_POST['quantidade'] ?? '';
    $imagemPath = $_POST['imagem'] ?? '';
    if ($novoNome && $novoPreco !== '') {
        if ($id) {
            $stmt = $conn->prepare('UPDATE produtos SET nome = ?, descricao = ?, preco = ?, quantidade = ?, imagem = ? WHERE id = ?');
            $stmt->bind_param('ssdisi', $novoNome, $novaDescricao, $novoPreco, $novaQuantidade, $imagemPath, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO produtos (nome, descricao, preco, quantidade, imagem) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('ssdis', $novoNome, $novaDescricao, $novoPreco, $novaQuantidade, $imagemPath);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: produtos.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Produto</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- menu hamburguer removido -->
    <div class="banner">Produto</div>
    <form method="POST" enctype="multipart/form-data">
        <h2><?= $id ? 'Editar Produto' : 'Novo Produto' ?></h2>
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required><br>
        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($descricao) ?></textarea><br>
        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($preco) ?>" required><br>
        <label>Quantidade:</label><br>
        <input type="number" name="quantidade" value="<?= htmlspecialchars($quantidade) ?>" required><br>
    <label>URL da Imagem:</label><br>
    <input type="url" name="imagem" value="<?= isset($imagemPath) ? htmlspecialchars($imagemPath) : '' ?>" placeholder="https://..." style="width:100%"><br>
        <button type="submit">Salvar</button>
        <div class="actions" style="margin-bottom: 10px;">
            <a href="produtos.php">Voltar</a>
        </div>
    </form>
    <!-- script do menu hamburguer removido -->
</body>
</html>