<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$erro = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $senha = $_POST['senha'] ?? '';

  if ($email!=='' && $senha!=='') {
    $st = $pdo->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email=:e LIMIT 1');
    $st->execute([':e'=>$email]);
    $u = $st->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($senha, $u['senha_hash'])) {
      session_start();
      $_SESSION['uid'] = (int)$u['id'];
      $_SESSION['nome'] = $u['nome'];
      $_SESSION['email'] = $u['email'];
      header('Location: /Federal_Jato/Federal-Jato/HTML/comandas.php');
      exit;
    } else {
      $erro = 'E-mail ou senha inválidos.';
    }
  } else {
    $erro = 'Informe e-mail e senha.';
  }
}
?>

<!doctype html>
<html>
  <head>
    <link rel="stylesheet" href="../CSS/style_criacao_de_login.css?v=1">
    <title>Pagina de Login</title>
  </head>

  <body>
    <div id="login-page">
      <div id="side-content">
        <img src="../Assets/Img/criacao_de_login.jpeg" alt="Imagem">
      </div>
      <div id="form-container">
        <h3 class="title">Fazer Login</h3>
        <div>
          <form>
            <input type="text" placeholder="Email" style="width: 400px;" />
            <input type="password" placeholder="Senha" />
            <button><a href="/Federal_Jato/Federal-Jato/HTML/comandas.php" style="cursor:pointer; color:blue;">Acessar</button></a>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>

<!-- <div id="content">
      <form>
        <div>
          <label for="email">Email</label>
          <input id="email" type="text" placeholder="Email" />
        </div>
        <div>
          <label for="password">Senha</label>
          <input id="password" type="password" placeholder="Password" />
        </div>

        <button type="button">Enviar</button>
      </form>
    </div> -->
