<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$itensPorPagina = 12;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$filtroCategoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : '';

// Carregar categorias para filtro
$categorias = [];
$catResult = $conn->query('SELECT id, nome FROM categorias ORDER BY nome');
while ($cat = $catResult->fetch_assoc()) {
    $categorias[] = $cat;
}

$where = [];
$params = [];
$types = '';
if ($busca !== '') {
    $where[] = 'nome LIKE ?';
    $params[] = "%$busca%";
    $types .= 's';
}
if ($filtroCategoria) {
    $where[] = 'categoria_id = ?';
    $params[] = $filtroCategoria;
    $types .= 'i';
}
$whereSQL = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Contar total
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM produtos $whereSQL");
if ($where) $stmtCount->bind_param($types, ...$params);
$stmtCount->execute();
$stmtCount->bind_result($totalProdutos);
$stmtCount->fetch();
$stmtCount->close();

$offset = ($pagina - 1) * $itensPorPagina;

// Buscar também o campo destaque
$sql = "SELECT id, nome, preco, quantidade, destaque FROM produtos $whereSQL ORDER BY id DESC LIMIT $itensPorPagina OFFSET $offset";
$stmt = $conn->prepare($sql);
if ($where) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$totalPaginas = max(1, ceil($totalProdutos / $itensPorPagina));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="produtos.css">
</head>
<body>
    <div class="banner">Produtos</div>
    <div class="menu-hamburguer">
    <button class="menu-icon" id="menuBtn" aria-label="Abrir menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <nav id="side-menu" class="side-menu">
            <div class="menu-header">
                <div class="user-avatar">
                    <span><?= strtoupper(substr($_SESSION['user_id'] ?? 'U',0,1)) ?></span>
                </div>
                <div class="saudacao">Olá, <strong><?php
                    $id = $_SESSION['user_id'] ?? 0;
                    $nome = '';
                    if ($id) {
                        $stmt = $conn->prepare('SELECT nome FROM usuarios WHERE id = ?');
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $stmt->bind_result($nome);
                        $stmt->fetch();
                        $stmt->close();
                    }
                    echo htmlspecialchars($nome);
                ?></strong></div>
            </div>
            <div class="menu-links">
                <a href="dashboard.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="usuario.php">Meus Dados</a>
            </div>
            <a href="logout.php" class="logout-link">Sair</a>
        </nav>
    </div>
    <div class="container container-produtos">
        <form method="get" style="display:flex; gap:12px; align-items:center; margin-bottom:18px; flex-wrap:wrap;">
            <input type="text" name="busca" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>" style="padding:8px; border-radius:4px; border:1px solid #4e944f;">
            <select name="categoria" style="padding:8px; border-radius:4px; border:1px solid #4e944f;">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filtroCategoria == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="background:#4e944f; color:#fff; border:none; border-radius:6px; padding:8px 18px; cursor:pointer;">Filtrar</button>
            <a href="produto.php" style="margin-left:auto; background:#4e944f; color:#fff; border:none; border-radius:6px; padding:8px 18px; text-decoration:none;">Novo Produto</a>
        </form>
        <div class="produtos-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="produto-card">
                <div class="produto-info">
                    <h3 class="titulo-destaque-loja" style="display:flex; align-items:center; gap:8px;">
                        <?= htmlspecialchars($row['nome']) ?>
                        <button class="btn-destaque" data-id="<?= $row['id'] ?>" data-destaque="<?= $row['destaque'] ?>" title="Destacar produto" style="background:none; border:none; cursor:pointer; font-size:1.3em; color:<?= $row['destaque'] ? '#FFD700' : '#bbb' ?>;">
                            <span class="star-icon" style="pointer-events:none;">&#9733;</span>
                        </button>
                    </h3>
                    <p><strong>ID:</strong> <?= $row['id'] ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($row['preco'], 2, ',', '.') ?></p>
                    <p><strong>Quantidade:</strong> <?= $row['quantidade'] ?></p>
                </div>
                <div class="actions">
                    <a href="produto.php?id=<?= $row['id'] ?>">Ver/Editar</a>
                    <a href="#" class="btn-excluir-produto" data-id="<?= $row['id'] ?>">Excluir</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin:24px 0 0 0; flex-wrap:wrap;">
            <?php if ($pagina > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina-1])) ?>" class="btn-detalhes">&laquo; Anterior</a>
            <?php endif; ?>
            <span style="font-size:1.1em; color:#4e944f; font-weight:500;">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina+1])) ?>" class="btn-detalhes">Próxima &raquo;</a>
            <?php endif; ?>
        </div>
        <div class="actions actions-voltar">
            <a href="index.php">Voltar</a>
        </div>
    </div>
    <script src="script.js"></script>
    <script>
    // AJAX para destacar produto
    document.querySelectorAll('.btn-destaque').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const atual = parseInt(this.getAttribute('data-destaque'));
            // Enviar requisição AJAX para destacar/desmarcar
            fetch('toggle_destaque.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id) + '&destaque=' + (atual ? 0 : 1)
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    // Atualiza visual
                    this.setAttribute('data-destaque', data.destaque);
                    this.style.color = data.destaque ? '#FFD700' : '#bbb';
                } else if (data.error) {
                    alert(data.error);
                }
            });
        });
    });
    </script>

<?php
// AJAX: alternar destaque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['destaque']) && basename($_SERVER['PHP_SELF']) === 'produtos.php') {
    $id = intval($_POST['id']);
    $destaque = intval($_POST['destaque']);
    // Limitar para no máximo 12 produtos em destaque
    if ($destaque === 1) {
        $qtd = $conn->query("SELECT COUNT(*) as qtd FROM produtos WHERE destaque = 1")->fetch_assoc()['qtd'];
        if ($qtd >= 12) {
            echo json_encode(['success' => false, 'error' => 'Limite de 12 produtos em destaque atingido.']);
            exit;
        }
    }
    $conn->query("UPDATE produtos SET destaque = $destaque WHERE id = $id");
    echo json_encode(['success' => true, 'destaque' => $destaque]);
    exit;
}
?>
<div id="popup-excluir" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; box-shadow:0 4px 24px #4e944f22; padding:32px 28px; min-width:320px; max-width:95vw; text-align:center;">
    <h3 class="titulo-destaque-loja" style="color:#a16207; margin-bottom:18px;">Confirmar exclusão</h3>
        <p style="margin-bottom:24px;">Tem certeza que deseja excluir este produto?</p>
        <div style="display:flex; gap:18px; justify-content:center;">
            <button id="btnConfirmarExcluir" style="background:#a16207; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-size:1em; cursor:pointer;">Excluir</button>
            <button id="btnCancelarExcluir" style="background:#4e944f; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-size:1em; cursor:pointer;">Cancelar</button>
        </div>
    </div>
</div>
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

    // Popup de exclusão customizado
    let idParaExcluir = null;
    document.querySelectorAll('.btn-excluir-produto').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            idParaExcluir = this.getAttribute('data-id');
            document.getElementById('popup-excluir').style.display = 'flex';
        });
    });
    document.getElementById('btnCancelarExcluir').onclick = function() {
        document.getElementById('popup-excluir').style.display = 'none';
        idParaExcluir = null;
    };
    document.getElementById('btnConfirmarExcluir').onclick = function() {
        if (idParaExcluir) {
            // Mantém filtros/página
            const params = new URLSearchParams(window.location.search);
            params.set('del', idParaExcluir);
            window.location.href = 'produtos.php?' + params.toString();
        }
    };
</script>
</body>
</html>
<?php
// Excluir produto
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM produtos WHERE id = $id");
    // Mantém filtros/página ao excluir
    $params = $_GET;
    unset($params['del']);
    $url = 'produtos.php';
    if ($params) $url .= '?' . http_build_query($params);
    header('Location: ' . $url);
    exit;
}
?>
