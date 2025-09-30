<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$stmtTipo = $conn->prepare('SELECT tipo FROM usuarios WHERE id = ?');
$stmtTipo->bind_param('i', $userId);
$stmtTipo->execute();
$stmtTipo->bind_result($tipoConta);
$stmtTipo->fetch();
$stmtTipo->close();
if ($tipoConta !== 'admin') {
    header('Location: loja.php');
    exit;
}
$id = $_GET['id'] ?? null;
// Carregar categorias
$categorias = [];
$catResult = $conn->query('SELECT id, nome FROM categorias ORDER BY nome');
while ($cat = $catResult->fetch_assoc()) {
    $categorias[] = $cat;
}

if ($id) {
    $stmt = $conn->prepare('SELECT id, nome, descricao, preco, quantidade, imagem, categoria_id FROM produtos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $nome, $descricao, $preco, $quantidade, $imagemPath, $categoriaId);
    $stmt->fetch();
    $stmt->close();
} else {
    $id = '';
    $nome = '';
    $descricao = '';
    $preco = '';
    $quantidade = '';
    $imagemPath = '';
    $categoriaId = '';
}
// Salvar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoNome = $_POST['nome'] ?? '';
    $novaDescricao = $_POST['descricao'] ?? '';
    $novoPreco = $_POST['preco'] ?? '';
    $novaQuantidade = $_POST['quantidade'] ?? '';
    $imagemPath = $_POST['imagem'] ?? '';
    $novaCategoria = $_POST['categoria_id'] ?? '';
    if ($novoNome && $novoPreco !== '') {
        if ($id) {
            $stmt = $conn->prepare('UPDATE produtos SET nome = ?, descricao = ?, preco = ?, quantidade = ?, imagem = ?, categoria_id = ? WHERE id = ?');
            $stmt->bind_param('ssdisii', $novoNome, $novaDescricao, $novoPreco, $novaQuantidade, $imagemPath, $novaCategoria, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO produtos (nome, descricao, preco, quantidade, imagem, categoria_id) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssdisi', $novoNome, $novaDescricao, $novoPreco, $novaQuantidade, $imagemPath, $novaCategoria);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: produtos.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Produto</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- menu hamburguer removido -->
    <div class="banner">Produto</div>
    <form method="POST" enctype="multipart/form-data">
    <h2 class="titulo-destaque-loja"><?= $id ? 'Editar Produto' : 'Novo Produto' ?></h2>
        <?php if ($id && !empty($imagemPath)): ?>
            <div style="text-align:center; margin-bottom:18px;">
                <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Imagem do produto" style="max-width:180px; max-height:180px; border-radius:10px; box-shadow:0 2px 12px #4e944f22;">
            </div>
        <?php endif; ?>
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required><br>
        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($descricao) ?></textarea><br>
        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($preco) ?>" required><br>
        <label>Quantidade:</label><br>
        <input type="number" name="quantidade" value="<?= htmlspecialchars($quantidade) ?>" required><br>
        <label>Categoria:</label><br>
        <div style="display:flex; gap:8px; align-items:center; margin-bottom:8px;">
            <select name="categoria_id" required style="flex:1;">
                <option value="">Selecione...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == ($categoriaId ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="btnNovaCategoria" style="background:#4e944f; color:#fff; border:none; border-radius:6px; padding:6px 12px; cursor:pointer;">Nova</button>
        </div>
        <label>URL da Imagem:</label><br>
        <input type="url" name="imagem" value="<?= htmlspecialchars($imagemPath) ?>" placeholder="https://..." style="width:100%"><br>
        <button type="submit">Salvar</button>
        <div class="actions" style="margin-bottom: 10px;">
            <a href="produtos.php">Voltar</a>
        </div>
    </form>
    <!-- Toast para nova categoria -->
    <div id="toastCategoria" class="toast-message" style="display:none; position:fixed; top:30px; left:50%; transform:translateX(-50%); z-index:9999;">
        <form id="formNovaCategoria" style="display:flex; gap:8px; align-items:center;">
            <input type="text" id="novaCategoriaNome" placeholder="Nome da categoria" required style="padding:8px; border-radius:4px; border:1px solid #4e944f;">
            <button type="submit" style="background:#4e944f; color:#fff; border:none; border-radius:6px; padding:6px 12px; cursor:pointer;">Salvar</button>
            <button type="button" id="fecharToast" style="background:#a16207; color:#fff; border:none; border-radius:6px; padding:6px 12px; cursor:pointer;">X</button>
        </form>
    </div>
</body>
<script>
// Toast Nova Categoria
const btnNovaCategoria = document.getElementById('btnNovaCategoria');
const toast = document.getElementById('toastCategoria');
const fecharToast = document.getElementById('fecharToast');
const formNovaCategoria = document.getElementById('formNovaCategoria');
const selectCategoria = document.querySelector('select[name="categoria_id"]');

btnNovaCategoria.onclick = () => { toast.style.display = 'block'; document.getElementById('novaCategoriaNome').focus(); };
fecharToast.onclick = () => { toast.style.display = 'none'; };
formNovaCategoria.onsubmit = async (e) => {
    e.preventDefault();
    const nome = document.getElementById('novaCategoriaNome').value.trim();
    if (!nome) return;
    // AJAX para salvar categoria
    const res = await fetch('salvar_categoria.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nome=' + encodeURIComponent(nome)
    });
    const data = await res.json();
    if (data.success && data.id) {
        // Adiciona no select e seleciona
        const opt = document.createElement('option');
        opt.value = data.id;
        opt.textContent = data.nome;
        opt.selected = true;
        selectCategoria.appendChild(opt);
        toast.style.display = 'none';
        document.getElementById('novaCategoriaNome').value = '';
    } else {
        alert(data.error || 'Erro ao salvar categoria');
    }
};
</script>
</html>