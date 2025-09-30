<?php
// loja.php - Tela principal para usuários comuns realizarem compras ou solicitar orçamento

session_start();
require_once "db.php";

// Buscar categorias e produtos agrupados por categoria
$categorias = [];
$sqlCat = "SELECT id, nome FROM categorias ORDER BY nome";
$resCat = $conn->query($sqlCat);
if ($resCat && $resCat->num_rows > 0) {
    while ($cat = $resCat->fetch_assoc()) {
        $categorias[$cat['id']] = $cat['nome'];
    }
}

$produtosPorCategoria = [];
if (!empty($categorias)) {
    foreach ($categorias as $catId => $catNome) {
        $sqlProd = "SELECT * FROM produtos WHERE categoria_id = $catId ORDER BY nome";
        $resProd = $conn->query($sqlProd);
        $produtosPorCategoria[$catId] = [];
        if ($resProd && $resProd->num_rows > 0) {
            while ($p = $resProd->fetch_assoc()) {
                $produtosPorCategoria[$catId][] = $p;
            }
        }
    }
}

?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Marcenaria</title>
    <link rel="stylesheet" href="loja.css">
</head>
<body>
    <div class="banner">Bem-vindo à Loja da Marcenaria</div>
    <?php
    // Produtos em destaque igual ao dashboard
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
    <div class="destaque-centralizado">
        <div class="produtos-destaque-wrapper">
            <h2 class="titulo-destaque-loja">Produtos em Destaque</h2>
            <div class="produtos-grid">
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
                                <form method="post" action="comprar.php">
                                    <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn-comprar">Comprar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </div>
    <main class="main-loja">
        <h2 class="titulo-loja">Produtos disponíveis</h2>
        <!-- Espaço de 25px entre destaque e carrosseis -->
        <div style="height:25px;"></div>
        <?php if (empty($categorias)): ?>
            <div>Nenhuma categoria cadastrada.</div>
        <?php else: ?>
            <?php foreach ($categorias as $catId => $catNome): ?>
                <section class="categoria-section">
                    <h3 class="titulo-destaque-loja categoria-titulo">Categoria: <?= htmlspecialchars($catNome) ?></h3>
                    <?php if (empty($produtosPorCategoria[$catId])): ?>
                        <div class="sem-produtos">Nenhum produto nesta categoria.</div>
                    <?php else: ?>
                        <div class="carousel-wrapper">
                            <div class="carousel" id="carousel-<?= $catId ?>">
                                <?php foreach ($produtosPorCategoria[$catId] as $p): ?>
                                    <div class="card-produto">
                                        <?php if (!empty($p['imagem'])): ?>
                                            <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="Imagem do produto">
                                        <?php endif; ?>
                                        <h4><?= htmlspecialchars($p['nome']) ?></h4>
                                        <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                                        <p><?= nl2br(htmlspecialchars($p['descricao'])) ?></p>
                                        <div class="actions-loja">
                                            <form method="get" action="produto.php" style="margin-bottom:6px;">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn-vermais">Ver mais</button>
                                            </form>
                                            <form method="post" action="comprar.php">
                                                <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn-comprar">Comprar</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-btn left" data-target="carousel-<?= $catId ?>" aria-label="Anterior">&#10094;</button>
                            <button class="carousel-btn right" data-target="carousel-<?= $catId ?>" aria-label="Próximo">&#10095;</button>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script>
    // Carrossel horizontal para cada categoria
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.carousel-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = btn.getAttribute('data-target');
                var carousel = document.getElementById(targetId);
                if (carousel) {
                    var scrollAmount = window.innerWidth < 700 ? 220 : 340;
                    if (btn.classList.contains('left')) {
                        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    } else {
                        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    }
                }
            });
        });
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
