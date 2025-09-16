<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
// Dados do usuário
$stmt = $conn->prepare('SELECT nome, email FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($nome, $email);
$stmt->fetch();
$stmt->close();
// Dados dos produtos
$produtos = $conn->query('SELECT COUNT(*) as total, SUM(quantidade) as estoque FROM produtos');
$dados = $produtos->fetch_assoc();
$produtos->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="banner">Bem-vindo à Marcenaria!</div>
    <div class="container">
        <h2>Olá, <?= htmlspecialchars($nome) ?>!</h2>
        <div class="info">
            <strong>Email:</strong> <?= htmlspecialchars($email) ?><br>
        </div>
        <hr style="margin: 18px 0;">
        <h3>Resumo dos Produtos</h3>
        <div class="info">
            <strong>Total de Produtos:</strong> <?= $dados['total'] ?? 0 ?><br>
            <strong>Estoque Total:</strong> <?= $dados['estoque'] ?? 0 ?><br>
        </div>
        <div class="actions" style="margin-top:28px;">
            <a href="produtos.php">Ver Produtos</a>
            <a href="usuario.php">Meus Dados</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
</body>
</html>
