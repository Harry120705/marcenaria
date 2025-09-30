<?php
// comprar.php - Processa compra de produto
session_start();
require_once "db.php";
$userId = $_SESSION['user_id'] ?? null;
if (!$userId || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$produtoId = intval($_POST['produto_id'] ?? 0);
if ($produtoId <= 0) {
    header('Location: loja.php');
    exit;
}
// Verifica se produto existe e tem estoque
$stmt = $conn->prepare('SELECT quantidade FROM produtos WHERE id = ?');
$stmt->bind_param('i', $produtoId);
$stmt->execute();
$stmt->bind_result($quantidade);
$stmt->fetch();
$stmt->close();
if ($quantidade < 1) {
    header('Location: loja.php?erro=sem_estoque');
    exit;
}
// Registrar compra (simples, pode ser melhorado)
$stmt = $conn->prepare('INSERT INTO compras (usuario_id, produto_id, data_compra) VALUES (?, ?, NOW())');
$stmt->bind_param('ii', $userId, $produtoId);
$stmt->execute();
$stmt->close();
// Atualizar estoque
$stmt = $conn->prepare('UPDATE produtos SET quantidade = quantidade - 1 WHERE id = ?');
$stmt->bind_param('i', $produtoId);
$stmt->execute();
$stmt->close();
header('Location: loja.php?sucesso=compra');
exit;
