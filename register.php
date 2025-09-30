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
    $senha2 = $_POST['senha2'] ?? '';

    function senha_forte($senha) {
        return strlen($senha) >= 8 &&
            preg_match('/[A-Z]/', $senha) &&
            preg_match('/[a-z]/', $senha) &&
            preg_match('/[0-9]/', $senha) &&
            preg_match('/[^a-zA-Z0-9]/', $senha);
    }

    if ($nome && $email && $senha && $senha2) {
        if ($senha !== $senha2) {
            $toast = 'As senhas não coincidem.';
            $toastType = 'error';
        } elseif (!senha_forte($senha)) {
            $toast = 'A senha deve ter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial.';
            $toastType = 'error';
        } else {
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
        }
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
    <link rel="stylesheet" href="register.css">
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
    <h2 class="titulo-destaque-loja">Cadastro de Usuário</h2>
        <form method="POST" id="formCadastro" autocomplete="off">
            <label>Nome:</label>
            <input type="text" name="nome" id="nome">
            <label>Email:</label>
            <input type="email" name="email" id="email">
            <label>Senha:</label>
            <input type="password" name="senha" id="senha">
            <label>Repita a Senha:</label>
            <input type="password" name="senha2" id="senha2">
            <button type="submit">Cadastrar</button>
        </form>
        <script>
        function showToast(msg, type = 'error') {
            var toast = document.createElement('div');
            toast.className = 'toast-message toast-' + type;
            toast.innerText = msg;
            toast.style.position = 'fixed';
            toast.style.top = '32px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.zIndex = 9999;
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.opacity = 0;
                setTimeout(function(){ toast.remove(); }, 600);
            }, 3200);
        }
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            var nome = document.getElementById('nome').value.trim();
            var email = document.getElementById('email').value.trim();
            var senha = document.getElementById('senha').value;
            var senha2 = document.getElementById('senha2').value;
            function senhaForte(s) {
                return s.length >= 8 && /[A-Z]/.test(s) && /[a-z]/.test(s) && /[0-9]/.test(s) && /[^a-zA-Z0-9]/.test(s);
            }
            if (!nome || !email || !senha || !senha2) {
                showToast('Por favor, preencha todos os campos.');
                e.preventDefault();
                return false;
            }
            if (senha !== senha2) {
                showToast('As senhas não coincidem.');
                e.preventDefault();
                return false;
            }
            if (!senhaForte(senha)) {
                showToast('A senha deve ter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial.');
                e.preventDefault();
                return false;
            }
        });
        </script>
        <div class="actions">
            <a href="login.php">Já tem conta? Entrar</a>
        </div>
    </div>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
</body>
</html>
