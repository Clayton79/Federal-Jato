<?php

$DB_HOST = '127.0.0.1';
$DB_NAME = 'federal_jato';
$DB_USER = 'root';
$DB_PASS = ''; 
$DB_CHAR = 'utf8mb4';

function pdo() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHAR;
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHAR";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt);
    }
    return $pdo;
}

function json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_param($arr, $key, $default = null) {
    return isset($arr[$key]) ? trim($arr[$key]) : $default;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = get_param($_REQUEST, 'action', 'list'); // list|create|update|delete|show

try {

    if ($method === 'GET' && $action === 'list') {
        // Listar serviços (com filtro opcional por nome)
        $q = get_param($_GET, 'q', '');
        if ($q !== '') {
            $stmt = pdo()->prepare("SELECT * FROM servicos WHERE nome LIKE ? ORDER BY id DESC");
            $stmt->execute(['%'.$q.'%']);
        } else {
            $stmt = pdo()->query("SELECT * FROM servicos ORDER BY id DESC");
        }
        $rows = $stmt->fetchAll();
        // Se o endpoint for chamado pelo navegador (sem fetch), pode renderizar HTML.
        // Por padrão, retorna JSON.
        json(['success'=>true,'data'=>$rows]);
    }

    if ($method === 'GET' && $action === 'show') {
        $id = (int) get_param($_GET, 'id', 0);
        if ($id <= 0) json(['success'=>false,'error'=>'ID inválido'], 400);
        $stmt = pdo()->prepare("SELECT * FROM servicos WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) json(['success'=>false,'error'=>'Não encontrado'], 404);
        json(['success'=>true,'data'=>$row]);
    }

    if ($method === 'POST' && $action === 'create') {
        // Aceita form-urlencoded ou multipart do formulário HTML
        $nome = get_param($_POST, 'nome', '');
        $preco = get_param($_POST, 'preco', '0');
        $descricao = get_param($_POST, 'descricao', '');

        if ($nome === '') json(['success'=>false,'error'=>'Nome é obrigatório'], 422);
        if (!is_numeric($preco)) json(['success'=>false,'error'=>'Preço inválido'], 422);

        $stmt = pdo()->prepare("INSERT INTO servicos (nome, preco, descricao) VALUES (?, ?, ?)");
        $stmt->execute([$nome, (float)$preco, $descricao]);

        // Redireciona de volta para a página de serviços se veio de um formulário
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit;
        }
        json(['success'=>true,'id'=>pdo()->lastInsertId()]);
    }

    if (($method === 'POST' || $method === 'PUT') && $action === 'update') {
        // Suporta PUT (JSON) e POST (form)
        if ($method === 'PUT') {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?: [];
            $id = (int) get_param($data, 'id', 0);
            $nome = get_param($data, 'nome', '');
            $preco = get_param($data, 'preco', '');
            $descricao = get_param($data, 'descricao', '');
        } else {
            $id = (int) get_param($_POST, 'id', 0);
            $nome = get_param($_POST, 'nome', '');
            $preco = get_param($_POST, 'preco', '');
            $descricao = get_param($_POST, 'descricao', '');
        }

        if ($id <= 0) json(['success'=>false,'error'=>'ID inválido'], 422);
        if ($nome === '') json(['success'=>false,'error'=>'Nome é obrigatório'], 422);
        if (!is_numeric($preco)) json(['success'=>false,'error'=>'Preço inválido'], 422);

        $stmt = pdo()->prepare("UPDATE servicos SET nome = ?, preco = ?, descricao = ?, atualizado_em = NOW() WHERE id = ?");
        $stmt->execute([$nome, (float)$preco, $descricao, $id]);
        json(['success'=>true]);
    }

    if (($method === 'POST' || $method === 'DELETE') && $action === 'delete') {
        // Aceita DELETE (JSON) e POST (form)
        if ($method === 'DELETE') {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?: [];
            $id = (int) get_param($data, 'id', 0);
        } else {
            $id = (int) get_param($_POST, 'id', 0);
        }
        if ($id <= 0) json(['success'=>false,'error'=>'ID inválido'], 422);

        $stmt = pdo()->prepare("DELETE FROM servicos WHERE id = ?");
        $stmt->execute([$id]);

        // Redireciona de volta se veio de formulário
        if (!empty($_SERVER['HTTP_REFERER']) && $method === 'POST') {
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit;
        }
        json(['success'=>true]);
    }

    // Ação não reconhecida
    json(['success'=>false,'error'=>'Ação não encontrada'], 404);

} catch (Throwable $e) {
    // Log simples (você pode melhorar para arquivo em backend/storage/logs)
    json(['success'=>false,'error'=>'Exceção: '.$e->getMessage()], 500);
}