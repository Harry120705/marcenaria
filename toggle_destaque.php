<?php
require 'db.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['jwt'])) {
    echo json_encode(['success' => false, 'error' => 'Não autenticado.']);
    exit;
}
if (!isset($_POST['id'], $_POST['destaque'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos.']);
    exit;
}
$id = intval($_POST['id']);
$destaque = intval($_POST['destaque']);
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