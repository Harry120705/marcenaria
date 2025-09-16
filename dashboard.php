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
    <div class="container">
        <div class="menu-hamburguer">
            <div class="menu-icon" onclick="toggleMenu()">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <nav id="side-menu" class="side-menu">
                <div class="actions" style="margin-bottom: 10px;">
                    <a href="produtos.php">Ver Produtos</a>
                    <a href="usuario.php">Meus Dados</a>
                    <a href="logout.php">Sair</a>
                </div>
            </nav>
        </div>
        <h2>Olá, <span class="user-name"><?= htmlspecialchars($nome) ?></span>!</h2>
        </header>
        <script>
            function toggleMenu() {
                document.getElementById('side-menu').classList.toggle('open');
            }
            document.addEventListener('click', function (e) {
                const menu = document.getElementById('side-menu');
                const icon = document.querySelector('.menu-icon');
                if (menu.classList.contains('open') && !menu.contains(e.target) && !icon.contains(e.target)) {
                    menu.classList.remove('open');
                }
            });
        </script>
        <hr style="margin: 18px 0;">
        <h3>Produtos em Destaque</h3>
        <div class="produtos-scroll" id="produtos-scroll">
            <?php
            $produtosCards = $conn->query('SELECT nome, preco, descricao, imagem FROM produtos LIMIT 12');
            if ($produtosCards->num_rows === 0): ?>
                <div class="nenhum-produto">Nenhum produto cadastrado.</div>
            <?php else:
                while ($p = $produtosCards->fetch_assoc()): ?>
                    <div class="produto-card-horizontal">
                        <div class="produto-img">
                            <img src="<?= $p['imagem'] ? htmlspecialchars($p['imagem']) : 'https://via.placeholder.com/180x120?text=Produto' ?>"
                                alt="<?= htmlspecialchars($p['nome']) ?>">
                        </div>
                        <div class="produto-card-info">
                            <h4><?= htmlspecialchars($p['nome']) ?></h4>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                            <p class="desc"><?= htmlspecialchars(mb_strimwidth($p['descricao'], 0, 60, '...')) ?></p>
                        </div>
                    </div>
                <?php endwhile;
            endif; ?>
        </div>
        <!-- Exemplo de outra seção de carrossel (pode duplicar para outras categorias) -->
        <!--
            <section class="produtos-scroll-section">
                <h3>Mais Vendidos</h3>
                <div class="produtos-scroll">
                    ...
                </div>
            </section>
            -->
    </div>
    <footer>&copy; <?php echo date('Y'); ?> Marcenaria Artesanal</footer>
</body>

</html>