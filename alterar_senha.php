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
    $novaSenha2 = $_POST['senha2'] ?? '';
    function senha_forte($senha) {
        return strlen($senha) >= 8 &&
            preg_match('/[A-Z]/', $senha) &&
            preg_match('/[a-z]/', $senha) &&
            preg_match('/[0-9]/', $senha) &&
            preg_match('/[^a-zA-Z0-9]/', $senha);
    }
    if ($novaSenha && $novaSenha2) {
        if ($novaSenha !== $novaSenha2) {
            echo '<p style="color:red;">As senhas não coincidem.</p>';
        } elseif (!senha_forte($novaSenha)) {
            echo '<p style="color:red;">A senha deve ter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial.</p>';
        } else {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $stmt->bind_param('si', $senhaHash, $id);
            $stmt->execute();
            $stmt->close();
            echo '<p>Senha alterada com sucesso!</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="alterar_senha.css">
</head>
<body>
    <div class="banner">Alterar Senha</div>
    <form method="POST" id="formAlterarSenha" autocomplete="off">
        <label>Nova Senha:</label><br>
        <input type="password" name="senha" id="senha" ><br>
        <label>Repita a Nova Senha:</label><br>
        <input type="password" name="senha2" id="senha2" ><br>
        <button type="submit">Alterar</button>
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
    document.getElementById('formAlterarSenha').addEventListener('submit', function(e) {
        var senha = document.getElementById('senha').value;
        var senha2 = document.getElementById('senha2').value;
        function senhaForte(s) {
            return s.length >= 8 && /[A-Z]/.test(s) && /[a-z]/.test(s) && /[0-9]/.test(s) && /[^a-zA-Z0-9]/.test(s);
        }
        if (!senha || !senha2) {
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
    <div class="actions" style="margin-top: 28px;">
        <a href="usuario.php?id=<?= $id ?>">Voltar</a>
    </div>
</body>
</html>
