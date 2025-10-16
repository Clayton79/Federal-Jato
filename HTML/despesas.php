<?php
declare(strict_types=1);
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Entrada e paginação */
$pagina     = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;

/* Totais (para cards simples) */
$st = $pdo->query("SELECT COALESCE(SUM(valor),0) AS total FROM despesas");
$totais = $st->fetch(PDO::FETCH_ASSOC) ?: ['total'=>0];

/* Lista paginada */
$st = $pdo->query("SELECT COUNT(*) FROM despesas");
$total_registros = (int)$st->fetchColumn();
$total_paginas   = max(1, (int)ceil($total_registros / $por_pagina));
$pagina          = min($pagina, $total_paginas);
$offset          = ($pagina - 1) * $por_pagina;

$sql = "SELECT id, data, descricao, valor
        FROM despesas
        ORDER BY data DESC, id DESC
        LIMIT :lim OFFSET :off";
$st = $pdo->prepare($sql);
$st->bindValue(':lim', $por_pagina, PDO::PARAM_INT);
$st->bindValue(':off', $offset, PDO::PARAM_INT);
$st->execute();
$despesas = $st->fetchAll(PDO::FETCH_ASSOC);

/* Helper QS */
function qs(array $o=[]): string { $q=$_GET; foreach($o as $k=>$v){$q[$k]=$v;} return http_build_query($q); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Despesas</title>
  <link rel="stylesheet" href="../CSS/style_despesas.css?v=1">
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="header-title">
      <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" fill="none" stroke-width="2" />
                    <circle cx="12" cy="7" r="4" stroke="white" fill="none" stroke-width="2" />
                </svg>
      <h1>Gestão de Despesas</h1>
    </div>
    <p class="header-subtitle">Acompanhe suas Despesas</p>
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

    <!-- Nova Despesa -->
    <section class="card">
      <div class="card-header">➕ Nova Despesa</div>
      <div class="card-body">
        <form method="POST" action="/Federal_Jato/Federal-Jato/PHP/controllers/despesas.php" class="form-grid">
          <input type="hidden" name="acao" value="criar">

          <div class="input-box col-2">
            <input type="text" name="descricao" class="input-clean" placeholder="Descrição" required>
          </div>

          <div class="input-box">
            <input type="number" step="0.01" name="valor" class="input-clean" placeholder="Valor (R$)" required>
          </div>

          <div class="input-box">
            <input type="date" name="data" class="input-clean" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div style="grid-column:1/-1;display:flex;gap:10px;justify-content:flex-start;margin-top:6px">
            <button class="btn primario" type="submit">Salvar</button>
            <a class="btn neutro" href="/Federal_Jato/Federal-Jato/HTML/despesas.php">Limpar</a>
          </div>
        </form>
      </div>
    </section>

    <!-- Painéis (mock preparados) -->

    <!-- Histórico de Despesas -->
    <section class="card">
      <div class="card-header">Histórico de Despesas</div>
      <div class="card-body">
        <div class="tabela-wrap">
          <table class="tabela">
            <thead>
              <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$despesas): ?>
              <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:22px">Nenhuma despesa cadastrada.</td></tr>
              <?php else: foreach ($despesas as $d): ?>
              <tr>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($d['data']))) ?></td>
                <td><?= htmlspecialchars($d['descricao']) ?></td>
                <td>R$ <?= number_format((float)$d['valor'], 2, ',', '.') ?></td>
                <td class="acoes">
                  <a class="acao danger" href="/Federal_Jato/Federal-Jato/PHP/controllers/despesas.php?acao=excluir&id=<?= (int)$d['id'] ?>" onclick="return confirm('Excluir despesa?')">Excluir</a>
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
      </div>
    </section>

  </main>
</body>
</html>
