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

  // Validação básica
  if ($nome==='' || $sobrenome==='' || $email==='' || $senha==='') {
    $erro = 'Preencha nome, sobrenome, e-mail e senha.';
  } else {
    // Normaliza e monta data de nascimento opcional
    $data_nasc = null;
    if ($dia!=='' && $mes!=='' && $ano!=='') {
      $dia = sprintf('%02d', (int)$dia);
      $mes = sprintf('%02d', (int)$mes);
      $ano = sprintf('%04d', (int)$ano);
      if (checkdate((int)$mes,(int)$dia,(int)$ano)) {
        $data_nasc = "$ano-$mes-$dia";
      }
    }

    // Verifica e-mail único
    $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
    $st->execute([':email'=>$email]);
    if ($st->fetch()) {
      $erro = 'E-mail já cadastrado.';
    } else {
      $hash = password_hash($senha, PASSWORD_DEFAULT);
      $ins = $pdo->prepare('
        INSERT INTO usuarios (nome, sobrenome, email, celular, senha_hash, data_nascimento, criado_em)
        VALUES (:nome, :sobrenome, :email, :celular, :hash, :data_nasc, NOW())
      ');
      $ins->execute([
        ':nome'       => $nome,
        ':sobrenome'  => $sobrenome,
        ':email'      => $email,
        ':celular'    => $celular ?: null,
        ':hash'       => $hash,
        ':data_nasc'  => $data_nasc
      ]);
      // Redireciona para login após cadastro
      header('Location: /Federal_Jato/Federal-Jato/HTML/login.php?ok=1');
      exit;
    }
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
        <h3 class="title">Nova Conta</h3>

        <?php if (!empty($erro)): ?>
          <div class="alert erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div>
          <form method="post" action="criacao_de_login.php" autocomplete="on">
            <div class="side-by-side">
              <input type="text" name="nome" placeholder="Nome" required />
              <input type="text" name="sobrenome" placeholder="Sobrenome" required />
            </div>

            <input type="email"   name="email"   placeholder="Email"   required />
            <input type="text"    name="celular" placeholder="Celular" />
            <input type="password"name="senha"   placeholder="Senha"   required />

            <div>
              <label>Data de Nascimento</label>
              <div class="birthday-input">
                <input type="text" name="dia" placeholder="Dia" />
                <input type="text" name="mes" placeholder="Mes" />
                <input type="text" name="ano" placeholder="Ano" />
              </div>
            </div>

            <button type="submit" class="btn primario">Salvar</button>
            <div style="margin-top:10px">
              <a href="/Federal_Jato/Federal-Jato/HTML/login.php" class="btn secundario" style="display:inline-block;text-align:center">Voltar ao login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
