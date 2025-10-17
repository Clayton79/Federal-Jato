<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ok = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome      = trim($_POST['nome'] ?? '');
  $sobrenome = trim($_POST['sobrenome'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $celular   = trim($_POST['celular'] ?? '');
  $senha     = $_POST['senha'] ?? '';
  $dia       = trim($_POST['dia'] ?? '');
  $mes       = trim($_POST['mes'] ?? '');
  $ano       = trim($_POST['ano'] ?? '');

  if ($nome==='' || $sobrenome==='' || $email==='' || $senha==='') {
    $erro = 'Preencha nome, sobrenome, e-mail e senha.';
  } else {
    $data_nasc = null;
    if ($dia!=='' && $mes!=='' && $ano!=='') {
      $dia = sprintf('%02d',(int)$dia);
      $mes = sprintf('%02d',(int)$mes);
      $ano = sprintf('%04d',(int)$ano);
      if (checkdate((int)$mes,(int)$dia,(int)$ano)) {
        $data_nasc = "$ano-$mes-$dia";
      }
    }
    $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
    $st->execute([':email'=>$email]);
    if ($st->fetch()) {
      $erro = 'E-mail já cadastrado.';
    } else {
      $hash = password_hash($senha, PASSWORD_DEFAULT);
      $ins = $pdo->prepare('
        INSERT INTO usuarios (nome, sobrenome, email, celular, senha_hash, data_nascimento, criado_em)
        VALUES (:nome,:sobrenome,:email,:celular,:hash,:data_nasc,NOW())
      ');
      $ins->execute([
        ':nome'=>$nome,
        ':sobrenome'=>$sobrenome,
        ':email'=>$email,
        ':celular'=>$celular ?: null,
        ':hash'=>$hash,
        ':data_nasc'=>$data_nasc
      ]);
      $ok = 'Conta criada! Agora faça login.';
    }
  }
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Nova Conta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/style_criacao_de_login.css?v=2">
  </head>
  <body>
    <div id="login-page">
      <div id="side-content">
        <img src="../Assets/Img/criacao_de_login.jpeg" alt="Imagem">
      </div>
      <div id="form-container">
        <h3 class="title">Nova Conta</h3>

        <?php if (!empty($erro)): ?>
          <div class="alert erro"><?= htmlspecialchars($erro) ?></div>
        <?php elseif (!empty($ok)): ?>
          <div class="alert ok"><?= htmlspecialchars($ok) ?></div>
        <?php endif; ?>

        <form method="post" action="criacao_de_login.php" autocomplete="on">
          <div class="side-by-side">
            <input type="text" name="nome" placeholder="Nome" required />
            <input type="text" name="sobrenome" placeholder="Sobrenome" required />
          </div>
          <input type="email" name="email" placeholder="Email" required />
          <input type="text" name="celular" placeholder="Celular" />
          <input type="password" name="senha" placeholder="Senha" required />

          <div>
            <label>Data de Nascimento</label>
            <div class="birthday-input">
              <input type="text" name="dia" placeholder="Dia" />
              <input type="text" name="mes" placeholder="Mes" />
              <input type="text" name="ano" placeholder="Ano" />
            </div>
          </div>

          <button type="submit" class="btn primario">Salvar</button>

          <div class="below-actions">
            <a href="login.php" class="btn primario btn-voltar">Voltar ao login</a>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
