<?php
require 'db.php';
session_start();
if (!isset($_SESSION['jwt'])) {
    header('Location: login.php');
    exit;
}
$result = $conn->query('SELECT id, nome, preco, quantidade FROM produtos');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="style.css">
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
        <div class="actions actions-produtos">
            <a href="produto.php">Novo Produto</a>
        </div>
        <div class="produtos-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="produto-card">
                <div class="produto-info">
                    <h3><?= htmlspecialchars($row['nome']) ?></h3>
                    <p><strong>ID:</strong> <?= $row['id'] ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($row['preco'], 2, ',', '.') ?></p>
                    <p><strong>Quantidade:</strong> <?= $row['quantidade'] ?></p>
                </div>
                <div class="actions">
                    <a href="produto.php?id=<?= $row['id'] ?>">Ver/Editar</a>
                    <a href="produtos.php?del=<?= $row['id'] ?>" onclick="return confirm('Excluir produto?')">Excluir</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="actions actions-voltar">
            <a href="index.php">Voltar</a>
        </div>
    </div>
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
<?php
// Excluir produto
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM produtos WHERE id = $id");
    header('Location: produtos.php');
    exit;
}
?>
