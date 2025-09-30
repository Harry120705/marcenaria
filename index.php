<?php
session_start();
if (isset($_SESSION['user_id'])) {
    require_once 'db.php';
    $id = $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($tipoConta);
    $stmt->fetch();
    $stmt->close();
    if ($tipoConta === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: loja.php');
    }
    exit;
}
// ...existing code...
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Marcenaria Artesanal - Sistema de Gestão</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="banner">
        Marcenaria Artesanal<br>
        <span class="subtitle"> Gestão, Qualidade e Criatividade em Madeira</span>
    </div>
    <div class="container">
    <h2 class="titulo-destaque-loja">Bem-vindo ao Sistema de Gestão de Marcenaria</h2>
        <div class="info">
            <p>Organize clientes, produtos e pedidos com facilidade.<br>
            Divulgue seus serviços, mostre seu portfólio e aumente sua produtividade!</p>
            <ul>
                <li>Controle de usuários e produtos</li>
                <li>Segurança com autenticação e senhas protegidas</li>
                <li>Gestão online, fácil e intuitiva</li>
            </ul>
        </div>
        <div class="actions">
            <a class="button" href="register.php">Registrar Novo Usuário</a>
            <a class="button" href="login.php">Entrar no Sistema</a>
        </div>
    </div>
    <footer>
        &copy; <?php echo date('Y'); ?> Marcenaria Artesanal. Todos os direitos reservados.
    </footer>
</body>
</html>