<?php
require 'db.php';

// Função para criptografar senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Cadastro de usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($nome && $email && $senha) {
        $senhaHash = hashPassword($senha);
        $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $nome, $email, $senhaHash);
        if ($stmt->execute()) {
            echo '<p>Usuário cadastrado com sucesso!</p>';
        } else {
            echo '<p>Erro ao cadastrar usuário: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    } else {
        echo '<p>Preencha todos os campos.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="banner" style="height:120px;font-size:1.3em;">Cadastre-se na Marcenaria</div>
    <div class="container">
        <h2>Cadastro de Usuário</h2>
        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Cadastrar</button>
        </form>
        <div class="actions">
            <a href="login.php">Já tem conta? Entrar</a>
        </div>
    </div>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
</body>
</html>
