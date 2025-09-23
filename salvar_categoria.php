<?php
require 'db.php';
header('Content-Type: application/json');
$nome = trim($_POST['nome'] ?? '');
if (!$nome) {
    echo json_encode(['success' => false, 'error' => 'Nome obrigatório']);
    exit;
}
// Verifica se já existe
$stmt = $conn->prepare('SELECT id FROM categorias WHERE nome = ?');
$stmt->bind_param('s', $nome);
$stmt->execute();
$stmt->bind_result($idExistente);
if ($stmt->fetch()) {
    $stmt->close();
    echo json_encode(['success' => true, 'id' => $idExistente, 'nome' => $nome]);
    exit;
}
$stmt->close();
// Insere nova categoria
$stmt = $conn->prepare('INSERT INTO categorias (nome) VALUES (?)');
$stmt->bind_param('s', $nome);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode(['success' => true, 'id' => $id, 'nome' => $nome]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar categoria']);
}
$stmt->close();
