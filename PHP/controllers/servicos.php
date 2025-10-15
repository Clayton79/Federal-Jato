<?php
// PHP/controllers/servicos.php (opcional)
require_once __DIR__.'/../config/db.php';
$pdo = pdo_conn();

function json($data, $code=200){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
function p($arr,$k,$d=null){ return isset($arr[$k]) ? trim($arr[$k]) : $d; }

$m = $_SERVER['REQUEST_METHOD'];
$a = p($_REQUEST,'action','list'); // list|show|create|update|delete

try {
  if ($m==='GET' && $a==='list') {
    $q = p($_GET,'q','');
    $categoria = p($_GET,'categoria','');
    $ordenar = strtolower(p($_GET,'ordenar',''));
    $order = 'ORDER BY id DESC';
    if (in_array($ordenar,['nome','preco','duracao'],true)) {
      $col = $ordenar==='duracao'?'duracao_min':$ordenar;
      $order = "ORDER BY $col ASC";
    }
    $w=[];$args=[];
    if ($q!==''){ $w[]='nome LIKE ?'; $args[]='%'.$q.'%'; }
    if ($categoria!=='' && $categoria!=='Filtrar por categoria'){ $w[]='categoria=?'; $args[]=$categoria; }
    $where = $w ? 'WHERE '.implode(' AND ',$w) : '';
    $st=$pdo->prepare("SELECT id,nome,descricao,preco,duracao_min,categoria FROM servicos $where $order");
    $st->execute($args);
    json(['success'=>true,'data'=>$st->fetchAll()]);
  }

  if ($m==='GET' && $a==='show') {
    $id=(int)p($_GET,'id',0);
    if ($id<=0) json(['success'=>false,'error'=>'ID inválido'],422);
    $st=$pdo->prepare("SELECT * FROM servicos WHERE id=?");
    $st->execute([$id]);
    $r=$st->fetch();
    if(!$r) json(['success'=>false,'error'=>'Não encontrado'],404);
    json(['success'=>true,'data'=>$r]);
  }

  if ($m==='POST' && $a==='create') {
    $nome=p($_POST,'nome',''); $preco=p($_POST,'preco',''); $descricao=p($_POST,'descricao','');
    $duracao=p($_POST,'duracao_min',''); $categoria=p($_POST,'categoria','');
    if ($nome==='') json(['success'=>false,'error'=>'Nome obrigatório'],422);
    if ($preco==='' || !is_numeric($preco)) json(['success'=>false,'error'=>'Preço inválido'],422);
    if ($duracao!=='' && !ctype_digit($duracao)) json(['success'=>false,'error'=>'Duração deve ser inteiro (min)'],422);
    $st=$pdo->prepare("INSERT INTO servicos (nome,descricao,preco,duracao_min,categoria) VALUES (?,?,?,?,?)");
    $st->execute([$nome,$descricao,(float)$preco,($duracao!==''?(int)$duracao:null),($categoria!==''?$categoria:null)]);
    if (!empty($_SERVER['HTTP_REFERER'])) { header('Location: '.$_SERVER['HTTP_REFERER']); http_response_code(303); exit; }
    json(['success'=>true,'id'=>$pdo->lastInsertId()],201);
  }

  if (($m==='POST' || $m==='PUT') && $a==='update') {
    $data = $m==='PUT' ? (json_decode(file_get_contents('php://input'),true)?:[]) : $_POST;
    $id=(int)p($data,'id',0);
    $nome=p($data,'nome',''); $preco=p($data,'preco',''); $descricao=p($data,'descricao','');
    $duracao=p($data,'duracao_min',''); $categoria=p($data,'categoria','');
    if ($id<=0) json(['success'=>false,'error'=>'ID inválido'],422);
    if ($nome==='') json(['success'=>false,'error'=>'Nome obrigatório'],422);
    if ($preco==='' || !is_numeric($preco)) json(['success'=>false,'error'=>'Preço inválido'],422);
    if ($duracao!=='' && !ctype_digit($duracao)) json(['success'=>false,'error'=>'Duração deve ser inteiro (min)'],422);
    $st=$pdo->prepare("UPDATE servicos SET nome=?,descricao=?,preco=?,duracao_min=?,categoria=?,atualizado_em=NOW() WHERE id=?");
    $st->execute([$nome,$descricao,(float)$preco,($duracao!==''?(int)$duracao:null),($categoria!==''?$categoria:null),$id]);
    json(['success'=>true]);
  }

  if (($m==='POST' || $m==='DELETE') && $a==='delete') {
    $id = $m==='DELETE' ? (int)p(json_decode(file_get_contents('php://input'),true)?:[],'id',0)
                        : (int)p($_POST,'id',0);
    if ($id<=0) json(['success'=>false,'error'=>'ID inválido'],422);
    $pdo->prepare("DELETE FROM servicos WHERE id=?")->execute([$id]);
    if ($m==='POST' && !empty($_SERVER['HTTP_REFERER'])) { header('Location: '.$_SERVER['HTTP_REFERER']); http_response_code(303); exit; }
    json(['success'=>true]);
  }

  json(['success'=>false,'error'=>'Ação não encontrada'],404);

} catch (Throwable $e) {
  json(['success'=>false,'error'=>'Erro: '.$e->getMessage()],500);
}