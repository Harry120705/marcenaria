<?php
// produto_detalhe.php - Retorna card HTML com detalhes do produto para AJAX
require_once "db.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div class="card-produto-detalhe"><div class="erro">Produto não encontrado.</div></div>';
    exit;
}
$sql = "SELECT p.*, c.nome as categoria FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = $id";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    echo '<div class="card-produto-detalhe"><div class="erro">Produto não encontrado.</div></div>';
    exit;
}
$p = $res->fetch_assoc();
?>
<div class="card-produto-detalhe">
    <button class="fechar-card" onclick="document.getElementById('card-produto-detalhe').remove()">&times;</button>
    <?php if (!empty($p['imagem'])): ?>
        <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="Imagem do produto" class="img-detalhe">
    <?php endif; ?>
    <h3><?= htmlspecialchars($p['nome']) ?></h3>
    <p class="categoria-produto">Categoria: <span><?= htmlspecialchars($p['categoria'] ?? 'Sem categoria') ?></span></p>
    <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
    <p><?= nl2br(htmlspecialchars($p['descricao'])) ?></p>
    <form method="post" action="comprar.php">
        <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
        <button type="submit" class="btn-comprar">Comprar</button>
    </form>
</div>
