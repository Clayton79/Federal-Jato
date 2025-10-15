<?php
// PHP/config/db.php
$DB_HOST = '127.0.0.1';
$DB_PORT = '3306';   // troque para '3307' se seu MySQL no XAMPP estiver nessa porta
$DB_NAME = 'federal_jato';
$DB_USER = 'root';
$DB_PASS = '';       // senha padrão do MySQL no XAMPP costuma ser vazio

function pdo_conn(): PDO {
  global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
  static $pdo = null;
  if ($pdo === null) {
    $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";
    $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
      $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $opt);
    } catch (Throwable $e) {
      // Em produção, logue o erro e mostre mensagem genérica
      die('Falha na conexão com o banco. Verifique DB/config e serviço MySQL.');
    }
  }
  return $pdo;
}
