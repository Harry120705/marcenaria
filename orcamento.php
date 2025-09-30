<?php
// orcamento.php - Processa solicitação de orçamento
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
// Registrar orçamento
$stmt = $conn->prepare('INSERT INTO orcamentos (usuario_id, produto_id, data_orcamento) VALUES (?, ?, NOW())');
$stmt->bind_param('ii', $userId, $produtoId);
$stmt->execute();
$stmt->close();
header('Location: loja.php?sucesso=orcamento');
exit;
