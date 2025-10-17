<?php
declare(strict_types=1);
require_once __DIR__.'/../config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$acao = $_REQUEST['acao'] ?? '';

try {
  if ($acao === 'criar' && $_SERVER['REQUEST_METHOD']==='POST') {
    $sql = "INSERT INTO despesas (data, descricao, valor) VALUES (:data,:descricao,:valor)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':data'      => $_POST['data'],
      ':descricao' => trim($_POST['descricao']),
      ':valor'     => (float)$_POST['valor'],
    ]);
    header('Location: /Federal_Jato/Federal-Jato/HTML/despesas.php'); exit;
  }

  if ($acao === 'excluir') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id>0){
      $pdo->prepare("DELETE FROM despesas WHERE id=:id")->execute([':id'=>$id]);
    }
    header('Location: /Federal_Jato/Federal-Jato/HTML/despesas.php'); exit;
  }

  header('Location: /Federal_Jato/Federal-Jato/HTML/despesas.php'); exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo 'Erro: '.$e->getMessage();
}