<?php
session_start();
if (!isset($_SESSION['usuario_logado'])) { header('Location: ../login.php'); exit; }

require_once '../db.class.php';
$db = new DB();
$pdo = $db->getConexao();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: FornecedorList.php?msg=excluido');
exit;