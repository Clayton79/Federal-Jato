<?php
declare(strict_types=1);

// Conexão
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Entrada
$data_ini  = $_GET['data_ini']  ?? '';
$data_fim  = $_GET['data_fim']  ?? '';
$situacao  = $_GET['situacao']  ?? '';
$servico  = $_GET['servico']  ?? '';
$veiculo   = $_GET['veiculo']   ?? '';
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;

// WHERE
$where  = [];
$params = [];

if ($data_ini !== '') { $where[] = 'DATE(data_hora) >= :data_ini'; $params[':data_ini'] = $data_ini; }
if ($data_fim !== '') { $where[] = 'DATE(data_hora) <= :data_fim'; $params[':data_fim'] = $data_fim; }
if ($servico  !== '') { $where[] = 'servico = :servico';  $params[':servico']  = $servico; }
if ($situacao !== '') { $where[] = 'situacao = :situacao';          $params[':situacao'] = $situacao; }
if ($veiculo  !== '') { $where[] = 'veiculo  = :veiculo';            $params[':veiculo']  = $veiculo; }


$whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

// Cards
$sqlCards = "
  SELECT
    COUNT(*) AS total_comandas,
    COALESCE(SUM(valor),0) AS valor_total,
    COALESCE(AVG(valor),0) AS ticket_medio
  FROM comandas
  $whereSql
";
$st = $pdo->prepare($sqlCards);
$st->execute($params);
$cards = $st->fetch(PDO::FETCH_ASSOC) ?: ['total_comandas'=>0,'valor_total'=>0,'ticket_medio'=>0];

// Total e paginação
$st = $pdo->prepare("SELECT COUNT(*) FROM comandas $whereSql");
$st->execute($params);
$total_registros = (int)$st->fetchColumn();
$total_paginas   = max(1, (int)ceil($total_registros / $por_pagina));
$pagina          = min($pagina, $total_paginas);
$offset          = ($pagina - 1) * $por_pagina;

// Lista
$sqlLista = "
  SELECT id, data_hora, veiculo, servico,valor, pagamento, situacao
  FROM comandas
  $whereSql
  ORDER BY data_hora DESC, id DESC
  LIMIT :lim OFFSET :off
";
$st = $pdo->prepare($sqlLista);
foreach ($params as $k=>$v) { $st->bindValue($k, $v, PDO::PARAM_STR); }
$st->bindValue(':lim', $por_pagina, PDO::PARAM_INT);
$st->bindValue(':off', $offset, PDO::PARAM_INT);
$st->execute();
$comandas = $st->fetchAll(PDO::FETCH_ASSOC);

// Helper QS
function qs(array $override=[]): string {
  $q = $_GET;
  foreach ($override as $k=>$v) { $q[$k]=$v; }
  return http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Histórico de Comandas</title>
  <link rel="stylesheet" href="../CSS/style_historico.css?v=2" />
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="header-title">
      <svg class="icon" fill="white" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke="white" fill="none" stroke-width="2"/>
    <polyline points="12 6 12 12 16 14" stroke="white" fill="none" stroke-width="2"/>
  </svg>
      <h1>Histórico de Comandas</h1>
    </div>
    <p class="header-subtitle">Acompanhamento de Histórico de Comandas</p>
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

    <!-- Filtros -->
    <form method="GET" action="historico.php" class="filtros-card">
      <div class="filtros-grid">
        <div class="filtro">
          <label>Data inicial</label>
          <input type="date" name="data_ini" value="<?= htmlspecialchars($data_ini) ?>">
        </div>
        <div class="filtro">
          <label>Data final</label>
          <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
        </div>
        <div class="filtro">
  <label>Serviço</label>
  <select name="servico">
    <option value="">Todos</option>
    <option value="Lavagem simples"  <?= (($_GET['servico'] ?? '')==='Lavagem simples')  ? 'selected' : '' ?>>Lavagem simples</option>
    <option value="Lavagem completa" <?= (($_GET['servico'] ?? '')==='Lavagem completa') ? 'selected' : '' ?>>Lavagem completa</option>
    <option value="Lavagem premium"  <?= (($_GET['servico'] ?? '')==='Lavagem premium')  ? 'selected' : '' ?>>Lavagem premium</option>
    <option value="Lavagem técnica"  <?= (($_GET['servico'] ?? '')==='Lavagem técnica')  ? 'selected' : '' ?>>Lavagem técnica</option>
    <option value="Lavagem a seco"   <?= (($_GET['servico'] ?? '')==='Lavagem a seco')   ? 'selected' : '' ?>>Lavagem a seco</option>
    <option value="Lavagem com cera" <?= (($_GET['servico'] ?? '')==='Lavagem com cera') ? 'selected' : '' ?>>Lavagem com cera</option>
    <option value="Lavagem de motor" <?= (($_GET['servico'] ?? '')==='Lavagem de motor') ? 'selected' : '' ?>>Lavagem de motor</option>
    <option value="Lavagem detalhada interna" <?= (($_GET['servico'] ?? '')==='Lavagem detalhada interna') ? 'selected' : '' ?>>Lavagem detalhada interna</option>
    <option value="Lavagem detalhada externa" <?= (($_GET['servico'] ?? '')==='Lavagem detalhada externa') ? 'selected' : '' ?>>Lavagem detalhada externa</option>
    <option value="Lavagem de rodas e caixas" <?= (($_GET['servico'] ?? '')==='Lavagem de rodas e caixas') ? 'selected' : '' ?>>Lavagem de rodas e caixas</option>
    <option value="Lavagem pós-viagem"        <?= (($_GET['servico'] ?? '')==='Lavagem pós-viagem')        ? 'selected' : '' ?>>Lavagem pós-viagem</option>
  </select>
</div>
        <div class="filtro">
          <label>Situação</label>
          <select name="situacao">
            <option value="">Todas</option>
            <option value="finalizada" <?= $situacao==='finalizada'?'selected':'' ?>>Finalizada</option>
            <option value="pendente"   <?= $situacao==='pendente'?'selected':'' ?>>Pendente</option>
            <option value="cancelada"  <?= $situacao==='cancelada'?'selected':'' ?>>Cancelada</option>
          </select>
        </div>
        <div class="filtro">
          <label>Veículo</label>
          <select name="veiculo">
            <option value="">Todos</option>
            <option value="carro" <?= $veiculo==='carro'?'selected':'' ?>>Carro</option>
            <option value="moto"  <?= $veiculo==='moto'?'selected':''  ?>>Moto</option>
          </select>
        </div>
        
      </div>

      <div class="filtros-acoes">
        <button class="btn primario" type="submit">Filtrar</button>
        <a class="btn secundario" href="historico.php">Limpar</a>
      </div>
    </form>

    <!-- Cards -->
    <section class="cards-resumo">
      <article class="card total">
        <div class="card-titulo">Total de Comandas</div>
        <div class="card-valor"><?= (int)$cards['total_comandas'] ?></div>
        <div class="card-icone">🗃️</div>
      </article>
      <article class="card valor">
        <div class="card-titulo">Valor Total</div>
        <div class="card-valor">R$ <?= number_format((float)$cards['valor_total'], 2, ',', '.') ?></div>
        <div class="card-icone">💵</div>
      </article>
      <article class="card ticket">
        <div class="card-titulo">Ticket Médio</div>
        <div class="card-valor">R$ <?= number_format((float)$cards['ticket_medio'], 2, ',', '.') ?></div>
        <div class="card-icone">📈</div>
      </article>
    </section>

    <!-- Título + Exportar -->
    <div class="bloco-titulo">
      <h2>Comandas Registradas</h2>
    </div>

    <!-- Tabela -->
    <div class="tabela-wrap">
      <table class="tabela">
        <thead>
          <tr>
            <th>ID</th>
            <th>Data/Hora</th>
            <th>Veículo</th>
            <th>Serviço</th>
            <th>Valor</th>
            <th>Pagamento</th>
            <th>Situação</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$comandas): ?>
            <tr class="row-placeholder">
              <td colspan="10">Nenhum registro encontrado com os filtros atuais.</td>
            </tr>
          <?php else: foreach ($comandas as $c): ?>
            <tr>
              <td>#<?= (int)$c['id'] ?></td>
              <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($c['data_hora']))) ?></td>
              <td><?= htmlspecialchars($c['veiculo'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['servico'] ?? '') ?></td>
              <td>R$ <?= number_format((float)$c['valor'], 2, ',', '.') ?></td>
              <td><?= htmlspecialchars($c['pagamento'] ?? '') ?></td>
              <td><span class="badge <?= htmlspecialchars(strtolower($c['situacao'])) ?>"><?= htmlspecialchars($c['situacao'] ?? '') ?></span></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <div class="paginacao">
      <a class="pag-btn <?= $pagina<=1?'desabilitado':'' ?>" href="<?= $pagina<=1?'#':'historico.php?'.qs(['pagina'=>$pagina-1]) ?>">Anterior</a>
      <?php
        $pags = array_unique(array_filter([max(1,$pagina-1), $pagina, min($total_paginas,$pagina+1)]));
        foreach ($pags as $p) {
          $cls = $p===$pagina ? 'pag-btn ativo' : 'pag-btn';
          echo '<a class="'.$cls.'" href="historico.php?'.qs(['pagina'=>$p]).'">'.$p.'</a>';
        }
      ?>
      <a class="pag-btn <?= $pagina>=$total_paginas?'desabilitado':'' ?>" href="<?= $pagina>=$total_paginas?'#':'historico.php?'.qs(['pagina'=>$pagina+1]) ?>">Próxima</a>
    </div>

  </main>
</body>
</html>
