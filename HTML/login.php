<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$erro = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $senha = $_POST['senha'] ?? '';
  if ($email !== '' && $senha !== '') {
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
    <meta charset="utf-8">
    <title>Fazer Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/style_login.css?v=2">
  </head>
  <body>
    <div id="login-page">
      <div id="side-content">
        <img src="../Assets/Img/criacao_de_login.jpeg" alt="Imagem">
      </div>
      <div id="form-container">
        <h3 class="title">Fazer Login</h3>

        <?php if (!empty($erro)): ?>
          <div class="alert erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="on">
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="senha" placeholder="Senha" required>

          <div class="login-actions">
            <button type="submit" class="btn primario btn-acessar">Acessar</button>
            <a href="criacao_de_login.php" class="btn-criar">Criar conta</a>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
