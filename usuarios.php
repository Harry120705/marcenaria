<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$stmtTipo = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ?');
$stmtTipo->bind_param('i', $userId);
$stmtTipo->execute();
$stmtTipo->bind_result($tipoConta);
$stmtTipo->fetch();
$stmtTipo->close();
if ($tipoConta !== 'admin') {
    header('Location: loja.php');
    exit;
}
$result = $conn->query('SELECT id, nome, email, criado_em FROM usuarios');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="usuarios.css">
</head>
<body>
    <h2 class="titulo-destaque-loja">Usuários</h2>
    <table border="1">
        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Criado em</th><th>Ações</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nome'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['criado_em'] ?></td>
            <td>
                <a href="usuario.php?id=<?= $row['id'] ?>">Ver</a> |
                <a href="usuarios.php?del=<?= $row['id'] ?>" onclick="return confirm('Excluir usuário?')">Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="register.php">Novo Usuário</a>
    <br><a href="index.php">Voltar</a>
</body>
</html>
<?php
// Excluir usuário
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM usuarios WHERE id = $id");
    header('Location: usuarios.php');
    exit;
}
?>
