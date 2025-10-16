<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Filtros e paginação */
$pagina     = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;
$situacao_f = $_GET['situacao'] ?? 'andamento'; // andamento|finalizada|todas

$where=[]; $params=[];
if ($situacao_f === 'andamento')    $where[] = "situacao = 'andamento'";
elseif ($situacao_f === 'finalizada') $where[] = "situacao = 'finalizada'";
$whereSql = $where ? 'WHERE '.implode(' AND ',$where) : '';

/* Total */
$st = $pdo->prepare("SELECT COUNT(*) FROM comandas $whereSql");
$st->execute($params);
$total_registros = (int)$st->fetchColumn();
$total_paginas   = max(1, (int)ceil($total_registros / $por_pagina));
$pagina          = min($pagina, $total_paginas);
$offset          = ($pagina - 1) * $por_pagina;

/* Lista */
$sql = "SELECT id, data_hora, veiculo, servico, categoria, valor, pagamento, situacao
        FROM comandas
        $whereSql
        ORDER BY data_hora DESC, id DESC
        LIMIT :lim OFFSET :off";
$st = $pdo->prepare($sql);
$st->bindValue(':lim', $por_pagina, PDO::PARAM_INT);
$st->bindValue(':off', $offset, PDO::PARAM_INT);
$st->execute();
$comandas = $st->fetchAll(PDO::FETCH_ASSOC);

/* Helper QS */
function qs(array $o=[]): string { $q=$_GET; foreach($o as $k=>$v){$q[$k]=$v;} return http_build_query($q); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Comandas</title>
  <link rel="stylesheet" href="../CSS/style_comandas.css?v=1">
</head>
<body>

  <!-- Header -->
<div class="header">
    <div class="header-title">
      <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" fill="none" stroke-width="2" />
                    <line x1="16" y1="2" x2="16" y2="6" stroke="white" stroke-width="2" />
                    <line x1="8" y1="2" x2="8" y2="6" stroke="white" stroke-width="2" />
                    <line x1="3" y1="10" x2="21" y2="10" stroke="white" stroke-width="2" />
                </svg>
      <h1>Gestão de Comandas</h1>
    </div>
    <p class="header-subtitle">Cadastro e acompanhamentos de serviços</p>
  </div>

<nav class="nav">
        <div class="nav-items">
            <a href="/Federal_Jato/Federal-Jato/HTML/comandas.php" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" fill="none" stroke-width="2" />
                    <line x1="16" y1="2" x2="16" y2="6" stroke="white" stroke-width="2" />
                    <line x1="8" y1="2" x2="8" y2="6" stroke="white" stroke-width="2" />
                    <line x1="3" y1="10" x2="21" y2="10" stroke="white" stroke-width="2" />
                </svg>
                Comandas
            </a>
            <a href="/Federal_Jato/Federal-Jato/HTML/despesas.php" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" fill="none" stroke-width="2" />
                    <circle cx="12" cy="7" r="4" stroke="white" fill="none" stroke-width="2" />
                </svg>
                Despesas
            </a>
            <a href="/Federal_Jato/Federal-Jato/HTML/historico.php" class="nav-item">
  <svg class="icon" fill="white" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke="white" fill="none" stroke-width="2"/>
    <polyline points="12 6 12 12 16 14" stroke="white" fill="none" stroke-width="2"/>
  </svg>
  Histórico
</a>

        </div>
        <a href="/Federal_Jato/Federal-Jato/HTML/login.php">
        <button class="logout-btn">Deslogar</button>
        </a>
    </nav>

  <main class="container">

    <!-- Nova Comanda -->
    <section class="card">
      <div class="card-header">➕ Nova Comanda</div>
      <div class="card-body">
        <form method="POST" action="/Federal_Jato/Federal-Jato/PHP/controllers/comandas.php" class="form-grid">
          <input type="hidden" name="acao" value="criar">

          <div class="select-box">
            <select name="veiculo" class="select-clean" required>
              <option value="">Veículo</option>
              <option value="carro">Carro</option>
              <option value="moto">Moto</option>
              <option value="outro">Outro</option>
            </select>
            <span class="select-caret">▾</span>
          </div>

          <div class="select-box">
            <select name="servico" class="select-clean" required>
              <option value="">Serviço</option>
              <option value="Lavagem completa">Lavagem completa</option>
              <option value="Polimento">Polimento</option>
              <option value="Higienização">Higienização</option>
            </select>
            <span class="select-caret">▾</span>
          </div>

          <div class="input-box">
            <input type="text" name="categoria" class="input-clean" placeholder="Categoria (ex.: lavagem)" required>
          </div>

          <div class="select-box">
            <select name="pagamento" class="select-clean" required>
              <option value="">Pagamento</option>
              <option value="pix">Pix</option>
              <option value="cartao">Cartão</option>
              <option value="dinheiro">Dinheiro</option>
            </select>
            <span class="select-caret">▾</span>
          </div>

          <div class="input-box">
            <input type="number" step="0.01" name="valor" class="input-clean" placeholder="Valor (R$)" required>
          </div>

          <div class="input-box">
            <input type="datetime-local" name="data_hora" class="input-clean" value="<?= date('Y-m-d\TH:i') ?>" required>
          </div>

          <div style="grid-column:1/-1;display:flex;gap:10px;justify-content:flex-start;margin-top:6px">
            <button class="btn primario" type="submit">Adicionar Comanda</button>
            <a class="btn neutro" href="/Federal_Jato/Federal-Jato/HTML/comandas.php">Limpar</a>
          </div>
        </form>
      </div>
    </section>

    <!-- Filtros rápidos -->
    <div class="section-title">
      <h2>Comandas</h2>
      <div>
        <a class="btn neutro" href="?<?= qs(['situacao'=>'andamento','pagina'=>1]) ?>">Andamento</a>
        <a class="btn neutro" href="?<?= qs(['situacao'=>'finalizada','pagina'=>1]) ?>">Finalizadas</a>
        <a class="btn neutro" href="?<?= qs(['situacao'=>'todas','pagina'=>1]) ?>">Todas</a>
      </div>
    </div>

    <!-- Tabela -->
    <div class="tabela-wrap card">
      <table class="tabela">
        <thead>
          <tr>
            <th>Veículo</th>
            <th>Serviço</th>
            <th>Categoria</th>
            <th>Data/Hora</th>
            <th>Valor</th>
            <th>Pagamento</th>
            <th>Situação</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$comandas): ?>
          <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:22px">Nenhuma comanda encontrada.</td></tr>
          <?php else: foreach ($comandas as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['veiculo'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['servico'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['categoria'] ?? '') ?></td>
            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['data_hora']))) ?></td>
            <td>R$ <?= number_format((float)$c['valor'],2,',','.') ?></td>
            <td><?= htmlspecialchars($c['pagamento'] ?? '') ?></td>
            <td><span class="badge <?= htmlspecialchars($c['situacao']) ?>"><?= htmlspecialchars($c['situacao']) ?></span></td>
            <td class="acoes">
              <?php if (($c['situacao'] ?? '')==='andamento'): ?>
                <a class="acao" href="/Federal_Jato/Federal-Jato/PHP/controllers/comandas.php?acao=finalizar&id=<?= (int)$c['id'] ?>" title="Finalizar">✅</a>
              <?php endif; ?>
              <a class="acao" href="/Federal_Jato/Federal-Jato/PHP/controllers/comandas.php?acao=excluir&id=<?= (int)$c['id'] ?>" onclick="return confirm('Excluir comanda?')" title="Excluir">🗑️</a>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <div class="paginacao">
      <a class="pag-btn <?= $pagina<=1?'desabilitado':'' ?>" href="<?= $pagina<=1?'#':'?'.qs(['pagina'=>$pagina-1]) ?>">Anterior</a>
      <?php
        $janela = array_unique(array_filter([max(1,$pagina-1),$pagina,min($total_paginas,$pagina+1)]));
        foreach($janela as $p){
          $cls = $p===$pagina?'pag-btn ativo':'pag-btn';
          echo '<a class="'.$cls.'" href="?'.qs(['pagina'=>$p]).'">'.$p.'</a>';
        }
      ?>
      <a class="pag-btn <?= $pagina>=$total_paginas?'desabilitado':'' ?>" href="<?= $pagina>=$total_paginas?'#':'?'.qs(['pagina'=>$pagina+1]) ?>">Próxima</a>
    </div>

  </main>
</body>
</html>
