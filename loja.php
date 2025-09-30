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

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Marcenaria</title>
    <link rel="stylesheet" href="loja.css">
    <script src="loja.js" defer></script>
</head>
<body>
    <div class="banner">
        <span class="banner-title">Bem-vindo à Loja da Marcenaria</span>
        <?php
        $usuarioLogado = false;
        $nomeUsuario = '';
        if (isset($_SESSION['jwt']) && isset($_SESSION['user_id'])) {
            $usuarioLogado = true;
            $stmt = $conn->prepare('SELECT nome FROM usuarios WHERE id = ?');
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($nomeUsuario);
            $stmt->fetch();
            $stmt->close();
        }
        ?>
        <div class="banner-actions">
            <?php if ($usuarioLogado): ?>
                <span class="saudacao-banner">Olá, <strong><?= htmlspecialchars($nomeUsuario) ?></strong></span>
            <?php else: ?>
                <a href="login.php" class="btn-banner-login">Login</a>
                <a href="register.php" class="btn-banner-cadastro">Cadastrar</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="menu-hamburguer">
        <button class="menu-icon" id="menuBtn" aria-label="Abrir menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <nav id="side-menu" class="side-menu">
            <div class="menu-header">
                <div class="user-avatar">
                    <img src="icons/Logo.png" alt="Logo Marcenaria" class="logo-img" style="height:32px;">
                </div>
            </div>
            <div class="menu-links">
                <a href="configuracoes.php">Configurações</a>
                <a href="orcamento.php">Orçamento</a>
            </div>
            <a href="logout.php" class="logout-link">Sair</a>
        </nav>
    </div>
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

</body>
</html>
