<?php
declare(strict_types=1);
require_once __DIR__.'/../config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$acao = $_REQUEST['acao'] ?? '';

try {
  if ($acao === 'criar' && $_SERVER['REQUEST_METHOD']==='POST') {
    $sql = "INSERT INTO comandas (data_hora, veiculo, servico, categoria, valor, pagamento, situacao)
            VALUES (:data_hora,:veiculo,:servico,:categoria,:valor,:pagamento,'andamento')";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':data_hora' => $_POST['data_hora'],
      ':veiculo'   => trim($_POST['veiculo']),
      ':servico'   => trim($_POST['servico']),
      ':categoria' => trim($_POST['categoria']),
      ':valor'     => (float)$_POST['valor'],
      ':pagamento' => trim($_POST['pagamento']),
    ]);
    header('Location: /Federal_Jato/Federal-Jato/HTML/comandas.php?situacao=andamento'); exit;
  }

  if ($acao === 'finalizar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id>0){
      $pdo->prepare("UPDATE comandas SET situacao='finalizada' WHERE id=:id")->execute([':id'=>$id]);
    }
    header('Location: /Federal_Jato/Federal-Jato/HTML/comandas.php?situacao=andamento'); exit;
  }

  if ($acao === 'excluir') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id>0){
      $pdo->prepare("DELETE FROM comandas WHERE id=:id")->execute([':id'=>$id]);
    }
    header('Location: /Federal_Jato/Federal-Jato/HTML/comandas.php'); exit;
  }

  header('Location: /Federal_Jato/Federal-Jato/HTML/comandas.php'); exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo 'Erro: '.$e->getMessage();
}