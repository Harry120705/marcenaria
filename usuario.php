<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$id = $_GET['id'] ?? $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT id, nome, email FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($id, $nome, $email);
$stmt->fetch();
$stmt->close();
// Atualizar dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoNome = $_POST['nome'] ?? '';
    $novoEmail = $_POST['email'] ?? '';
    if ($novoNome && $novoEmail) {
        $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
        $stmt->bind_param('ssi', $novoNome, $novoEmail, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: usuario.php?id=' . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dados do Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="banner">Usuário</div>
    <form method="POST">
        <h2>Dados do Usuário</h2>
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required><br>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>
        <button type="submit">Salvar</button>
        <div class="actions" style="margin-top: 1px;">
            <a href="alterar_senha.php?id=<?= $id ?>">Alterar Senha</a>
            <a href="index.php">Voltar</a>
        </div>
    </form>

</body>

</html>