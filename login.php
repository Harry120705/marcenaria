<?php
require 'db.php';
session_start();

// Função para gerar JWT simples
function generateJWT($userId, $email) {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode(['id' => $userId, 'email' => $email, 'exp' => time() + 3600]));
    $secret = 'secreto123'; // Troque por uma chave forte
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
    return "$header.$payload.$signature";
}

// Mensagem para toast
$toast = '';
$toastType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $stmt = $conn->prepare('SELECT id, senha FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $senhaHash);
            $stmt->fetch();
            if (password_verify($senha, $senhaHash)) {
                $jwt = generateJWT($id, $email);
                $_SESSION['jwt'] = $jwt;
                $_SESSION['user_id'] = $id;
                header('Location: dashboard.php');
                exit;
            } else {
                $toast = 'Senha incorreta.';
                $toastType = 'error';
            }
        } else {
            $toast = 'Email não encontrado.';
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
    <title>Login</title>
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
    <div class="banner" style="height:120px;font-size:1.3em;">Entrar na Marcenaria</div>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="actions">
            <a href="register.php">Não tem conta? Cadastre-se</a>
        </div>
    </div>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
</body>
</html>
