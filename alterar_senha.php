<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$id = $_GET['id'] ?? $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['senha'] ?? '';
    if ($novaSenha) {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->bind_param('si', $senhaHash, $id);
        $stmt->execute();
        $stmt->close();
        echo '<p>Senha alterada com sucesso!</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Alterar Senha</h2>
    <form method="POST">
        <label>Nova Senha:</label><br>
        <input type="password" name="senha" required><br>
        <button type="submit">Alterar</button>
    </form>
    <a href="usuario.php?id=<?= $id ?>">Voltar</a>
</body>
</html>
