<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="historico.csv"');

$data_ini  = $_GET['data_ini']  ?? '';
$data_fim  = $_GET['data_fim']  ?? '';
$situacao  = $_GET['situacao']  ?? '';
$veiculo   = $_GET['veiculo']   ?? '';
$categoria = $_GET['categoria'] ?? '';

$where=[];$params=[];
if ($data_ini!==''){ $where[]='DATE(data_hora)>=:data_ini'; $params[':data_ini']=$data_ini; }
if ($data_fim!==''){ $where[]='DATE(data_hora)<=:data_fim'; $params[':data_fim']=$data_fim; }
if ($situacao!==''){ $where[]='situacao=:situacao';        $params[':situacao']=$situacao; }
if ($veiculo !==''){ $where[]='veiculo=:veiculo';          $params[':veiculo']=$veiculo; }
if ($categoria!==''){ $where[]='categoria=:categoria';      $params[':categoria']=$categoria; }

$whereSql = $where ? ('WHERE '.implode(' AND ',$where)) : '';

$sql = "SELECT id,data_hora,veiculo,servico,categoria,valor,pagamento,situacao
        FROM comandas $whereSql ORDER BY data_hora DESC,id DESC";
$st = $pdo->prepare($sql);
$st->execute($params);

$out = fopen('php://output','w');
fputcsv($out, ['ID','Data/Hora','Veículo','Serviço','Categoria','Valor','Pagamento','Situação'], ';');

while($r = $st->fetch(PDO::FETCH_ASSOC)){
  fputcsv($out, [
    $r['id'],
    date('d/m/Y H:i', strtotime($r['data_hora'])),
    $r['veiculo'],
    $r['servico'],
    $r['categoria'],
    number_format((float)$r['valor'],2,',','.'),
    $r['pagamento'],
    $r['situacao']
  ], ';');
}
fclose($out);
