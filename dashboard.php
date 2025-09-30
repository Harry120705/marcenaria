<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
// Dados do usuário
// Buscar nome, email e tipo de conta
$stmt = $conn->prepare('SELECT nome, email, tipo FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($nome, $email, $tipoConta);
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
    <link rel="stylesheet" href="dashboard.css">
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
    <div class="destaque-centralizado">
        <div class="produtos-destaque-wrapper">
            <h2 class="titulo-destaque-loja">Produtos em Destaque</h2>
            <div class="produtos-grid">
                <?php
                $produtosDestaque = [];
                $sqlDestaque = "SELECT p.id, p.nome, p.preco, p.descricao, p.imagem, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.destaque = 1 LIMIT 12";
                $resDestaque = $conn->query($sqlDestaque);
                if ($resDestaque) {
                    while ($row = $resDestaque->fetch_assoc()) {
                        $produtosDestaque[] = $row;
                    }
                }
                $countDestaque = count($produtosDestaque);
                if ($countDestaque < 12) {
                    $idsDestaque = array_column($produtosDestaque, 'id');
                    $idsStr = $idsDestaque ? implode(',', $idsDestaque) : '0';
                    $faltam = 12 - $countDestaque;
                    $sqlMenorEstoque = "SELECT p.id, p.nome, p.preco, p.descricao, p.imagem, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id NOT IN ($idsStr) ORDER BY p.quantidade ASC LIMIT $faltam";
                    $resMenorEstoque = $conn->query($sqlMenorEstoque);
                    if ($resMenorEstoque) {
                        while ($row = $resMenorEstoque->fetch_assoc()) {
                            $produtosDestaque[] = $row;
                        }
                    }
                }
                ?>
                <?php if (count($produtosDestaque) === 0): ?>
                    <div class="nenhum-produto">Nenhum produto em destaque.</div>
                <?php else:
                    foreach ($produtosDestaque as $p): ?>
                        <div class="produto-card dashboard-card" data-id="<?= $p['id'] ?>">
                            <div class="produto-info">
                                <h3 class="titulo-destaque-loja"><?= htmlspecialchars($p['nome']) ?></h3>
                                <p class="categoria-produto">Categoria: <span><?= htmlspecialchars($p['categoria'] ?? 'Sem categoria') ?></span></p>
                                <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                                <form method="get" action="produto.php" style="margin-bottom:6px;">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn-vermais">Ver mais</button>
                                </form>
                                    <!-- Botão de compra removido para admin -->
                            </div>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </div>
    <div class="atualizacoes-dashboard" style="margin-top:32px;">
        <!-- Espaço reservado para futuras atualizações, avisos ou novidades -->
    </div>
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

            // Card de produto dinâmico (Ver mais)
            function abrirCardProduto(id) {
                // Remove card anterior se existir
                let cardExistente = document.getElementById('card-produto-detalhe');
                if (cardExistente) cardExistente.remove();
                // Cria overlay
                let overlay = document.createElement('div');
                overlay.id = 'card-produto-detalhe';
                overlay.className = 'card-produto-detalhe-overlay';
                overlay.innerHTML = '<div class="card-produto-detalhe"><div class="carregando">Carregando...</div></div>';
                document.body.appendChild(overlay);
                // AJAX para buscar dados
                fetch('produto_detalhe.php?id=' + id)
                    .then(r => r.text())
                    .then(html => {
                        overlay.innerHTML = html;
                    });
                // Fecha ao clicar fora
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) overlay.remove();
                });
            }
            // Adiciona evento aos botões Ver mais
            window.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.btn-vermais').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        let id = btn.parentElement.querySelector('input[name="id"]').value;
                        abrirCardProduto(id);
                    });
                });
            });
        </script>
</body>

</html>