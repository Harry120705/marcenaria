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
    <div class="banner">Marcenaria Magalu</div>
    <div class="menu-hamburguer">
    <button class="menu-icon" id="menuBtn" aria-label="Abrir menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <nav id="side-menu" class="side-menu">
            <div class="menu-header">
                <div class="user-avatar">
                    <span><?= strtoupper(substr($nome,0,1)) ?></span>
                </div>
                <div class="saudacao">Olá, <strong><?= htmlspecialchars($nome) ?></strong></div>
            </div>
            <div class="menu-links">
                <a href="dashboard.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="usuario.php">Meus Dados</a>
            </div>
            <a href="logout.php" class="logout-link">Sair</a>
        </nav>
    </div>
    <main class="main-loja">
        <h3 class="titulo-dashboard">Produtos em Destaque</h3>
        <div class="produtos-grid">
            <?php
            $produtosCards = $conn->query('SELECT nome, preco, descricao, imagem FROM produtos LIMIT 12');
            if ($produtosCards->num_rows === 0): ?>
                <div class="nenhum-produto">Nenhum produto cadastrado.</div>
            <?php else:
                while ($p = $produtosCards->fetch_assoc()): ?>
                    <div class="produto-card">
                        <div class="produto-img">
                            <img src="<?= $p['imagem'] ? htmlspecialchars($p['imagem']) : 'https://via.placeholder.com/220x140?text=Produto' ?>"
                                alt="<?= htmlspecialchars($p['nome']) ?>">
                        </div>
                        <div class="produto-info">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                            <p class="desc-produto-dashboard"><?= htmlspecialchars(mb_strimwidth($p['descricao'], 0, 70, '...')) ?></p>
                        </div>
                    </div>
                <?php endwhile;
            endif; ?>
        </div>
    </main>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
    <script src="script.js"></script>
    <script>
        function toggleMenu() {
            document.getElementById('side-menu').classList.toggle('open');
            document.getElementById('menuBtn').classList.toggle('active');
        }
        document.addEventListener('click', function (e) {
            const menu = document.getElementById('side-menu');
            const icon = document.getElementById('menuBtn');
            if (menu.classList.contains('open') && !menu.contains(e.target) && !icon.contains(e.target)) {
                menu.classList.remove('open');
                icon.classList.remove('active');
            }
        });
    </script>
</body>

</html>