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
            // Buscar produtos com categoria
            $produtosCards = $conn->query('SELECT p.id, p.nome, p.preco, p.descricao, p.imagem, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id LIMIT 12');
            if ($produtosCards->num_rows === 0): ?>
                <div class="nenhum-produto">Nenhum produto cadastrado.</div>
            <?php else:
                while ($p = $produtosCards->fetch_assoc()): ?>
                    <div class="produto-card dashboard-card" data-id="<?= $p['id'] ?>">
                        <div class="produto-info">
                            <h3><?= htmlspecialchars($p['nome']) ?></h3>
                            <p class="categoria-produto">Categoria: <span><?= htmlspecialchars($p['categoria'] ?? 'Sem categoria') ?></span></p>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                            <button class="btn-detalhes" data-id="<?= $p['id'] ?>">Ver detalhes</button>
                        </div>
                    </div>
                    <div class="detalhe-modal" id="detalhe-<?= $p['id'] ?>">
                        <div class="detalhe-card">
                            <button class="fechar-modal">&times;</button>
                            <h2><?= htmlspecialchars($p['nome']) ?></h2>
                            <p class="categoria-produto">Categoria: <span><?= htmlspecialchars($p['categoria'] ?? 'Sem categoria') ?></span></p>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                            <?php if (!empty($p['imagem'])): ?>
                                <div style="text-align:center; margin:18px 0;">
                                    <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="Imagem do produto" style="max-width:220px; max-height:180px; border-radius:10px; box-shadow:0 2px 12px #4e944f22;">
                                </div>
                            <?php endif; ?>
                            <p class="desc-produto-dashboard" style="font-size:1.15em; color:#222; margin-top:18px;"> <?= nl2br(htmlspecialchars($p['descricao'])) ?> </p>
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

        // Modal detalhes produto
        document.querySelectorAll('.btn-detalhes').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const modal = document.getElementById('detalhe-' + id);
                if (modal) {
                    modal.classList.add('show');
                }
            });
        });
        document.querySelectorAll('.fechar-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.detalhe-modal').classList.remove('show');
            });
        });
        // Fechar ao clicar fora
        document.querySelectorAll('.detalhe-modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('show');
            });
        });
    </script>
</body>

</html>