<?php
require 'db.php';

// Função para criptografar senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Cadastro de usuário
// Mensagem para toast
$toast = '';
$toastType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($nome && $email && $senha) {
        $senhaHash = hashPassword($senha);
        $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $nome, $email, $senhaHash);
        if ($stmt->execute()) {
            $toast = 'Usuário cadastrado com sucesso!';
            $toastType = 'success';
        } else {
            $toast = 'Erro ao cadastrar usuário: ' . $stmt->error;
            $toastType = 'error';
        }
        $stmt->close();
    } else {
        $toast = 'Preencha todos os campos.';
        $toastType = 'error';
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
    <?php if ($toast): ?>
        <div class="toast-message toast-<?= $toastType ?>">
            <?= htmlspecialchars($toast) ?>
        </div>
        <script>
            setTimeout(function(){
                var toast = document.querySelector('.toast-message');
                if (toast) toast.style.display = 'none';
            }, 3500);
        </script>
    <?php endif; ?>
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
