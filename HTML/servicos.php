<?php
// Topo: lógica sem JS (criar, excluir, filtrar, listar)
require_once __DIR__.'/../PHP/config/db.php';
$pdo = pdo_conn();

$erros = [];

// CREATE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['acao']??'')==='criar') {
  $nome        = trim($_POST['nome']??'');
  $preco       = trim($_POST['preco']??'');
  $descricao   = trim($_POST['descricao']??'');
  $duracao_min = trim($_POST['duracao_min']??'');
  $categoria   = trim($_POST['categoria']??'');

  if ($nome==='') $erros[]='Nome é obrigatório';
  if ($preco==='' || !is_numeric($preco)) $erros[]='Preço inválido';
  if ($duracao_min!=='' && !ctype_digit($duracao_min)) $erros[]='Duração deve ser inteiro (min)';

  if (!$erros) {
    $st=$pdo->prepare("INSERT INTO servicos (nome, descricao, preco, duracao_min, categoria) VALUES (?,?,?,?,?)");
    $st->execute([
      $nome,
      $descricao,
      (float)$preco,
      ($duracao_min!==''?(int)$duracao_min:null),
      ($categoria!==''?$categoria:null)
    ]);
    header('Location: servicos.php'); exit;
  }
}

// DELETE
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['acao']??'')==='excluir') {
  $id=(int)($_POST['id']??0);
  if ($id>0){ $pdo->prepare("DELETE FROM servicos WHERE id=?")->execute([$id]); }
  header('Location: servicos.php'); exit;
}

// FILTROS/ORDENAÇÃO
$q         = trim($_GET['q']??'');
$categoria = trim($_GET['categoria']??'');
$ordenar   = trim($_GET['ordenar']??'');

$order = 'ORDER BY id DESC';
if (in_array($ordenar,['nome','preco','duracao'],true)) {
  $col = $ordenar==='duracao' ? 'duracao_min' : $ordenar;
  $order = "ORDER BY $col ASC";
}

$where=[]; $args=[];
if ($q!==''){ $where[]='nome LIKE ?'; $args[]='%'.$q.'%'; }
if ($categoria!=='' && $categoria!=='Filtrar por categoria'){ $where[]='categoria=?'; $args[]=$categoria; }
$whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$st=$pdo->prepare("SELECT id,nome,descricao,preco,duracao_min,categoria FROM servicos $whereSql $order");
$st->execute($args);
$servicos=$st->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Serviços</title>
    <!-- Caminho relativo: HTML -> CSS -->
    <link rel="stylesheet" href="../CSS/style_servicos.css?v=3">
</head>
<body>
    
    <div class="header">
        <div class="header-title">
            <svg class="icon" fill="white" viewBox="0 0 24 24">
                <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
            </svg>
            <h1>Gestão de Serviços</h1>
        </div>
        <p class="header-subtitle">Cadastro e acompanhamento de serviços</p>
    </div>

    <nav class="nav">
        <div class="nav-items">
            <a href="#" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" fill="none" stroke-width="2"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke="white" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke="white" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke="white" stroke-width="2"/>
                </svg>
                Comandas
            </a>
            <a href="#" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" fill="none" stroke-width="2"/>
                    <circle cx="12" cy="7" r="4" stroke="white" fill="none" stroke-width="2"/>
                </svg>
                Depesas
            </a>
            <a href="#" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
                </svg>
                Serviços
            </a>
            <a href="#" class="nav-item">
                <svg class="icon" fill="white" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="white" fill="none" stroke-width="2"/>
                    <polyline points="12 6 12 12 16 14" stroke="white" fill="none" stroke-width="2"/>
                </svg>
                Histórico
            </a>
        </div>
        <button class="logout-btn">Deslogar</button>
    </nav>

    <div class="container">
        
        <!-- Botão/área para novo serviço: formulário sem JS -->
        <form method="POST" action="servicos.php" class="new-service-btn" style="display:flex;gap:8px;align-items:center;margin-bottom:16px;">
            <input type="hidden" name="acao" value="criar">
            <span style="margin-right:12px;">Novo Serviço</span>
            <!-- Campos inline para criar rápido -->
            <input type="text" name="nome" placeholder="Nome do Serviço" required>
            <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
            <input type="number" name="duracao_min" placeholder="Duração (min)">
            <input type="text" name="categoria" placeholder="Categoria">
            <input type="text" name="descricao" placeholder="Descrição" style="min-width:220px;">
            <button type="submit" class="logout-btn" style="margin-left:8px;">Salvar</button>
        </form>

        <?php if (!empty($erros)): ?>
          <div style="background:#ffe0e0;color:#900;padding:10px 12px;border-radius:6px;margin:12px 0;">
            <?= htmlspecialchars(implode(' • ', $erros)) ?>
          </div>
        <?php endif; ?>

        <!-- Cards/estatísticas (exemplo simples; você pode calcular com COUNT/SUM se quiser) -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Serviços</h3>
                <p>Todos os serviços cadastrados</p>
                <div class="value"><?= count($servicos) ?></div>
            </div>
            <div class="stat-card">
                <h3>Categorias</h3>
                <p>Diversidade de Serviços</p>
                <div class="value">
                  <?php
                    $cats = array_filter(array_unique(array_map(fn($s)=>$s['categoria']??'', $servicos)));
                    echo count($cats);
                  ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Mais Popular</h3>
                <p>Serviço mais solicitado</p>
                <div class="value" style="font-size: 24px; margin-top: 8px;">
                  <!-- Placeholder estático; substitua com cálculo real se tiver histórico -->
                  Lavagem Completa
                </div>
            </div>
        </div>

        <!-- Filtros/ordem (GET) -->
        <div class="filters">
            <div class="filters-row">
                <form method="GET" action="servicos.php" style="display:flex;gap:12px;flex-wrap:wrap;width:100%;">
                    <input type="text" class="search-input" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Pesquisar Serviço...">
                    <select class="filter-select" name="categoria">
                        <option <?= $categoria===''?'selected':'' ?>>Filtrar por categoria</option>
                        <option <?= $categoria==='Lavagem'?'selected':'' ?>>Lavagem</option>
                        <option <?= $categoria==='Polimento'?'selected':'' ?>>Polimento</option>
                        <option <?= $categoria==='Manutenção'?'selected':'' ?>>Manutenção</option>
                    </select>
                    <select class="filter-select" name="ordenar">
                        <option value="">Ordenar por</option>
                        <option value="nome"    <?= $ordenar==='nome'?'selected':'' ?>>Nome</option>
                        <option value="preco"   <?= $ordenar==='preco'?'selected':'' ?>>Preço</option>
                        <option value="duracao" <?= $ordenar==='duracao'?'selected':'' ?>>Duração</option>
                    </select>
                    <button class="logout-btn" type="submit">Aplicar</button>
                </form>
            </div>
        </div>

        <!-- Seção Tabela -->
        <div class="services-section">
            <div class="services-header">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    Todos os Serviços
                </h2>
                <span class="services-count"><?= count($servicos) ?> Serviços Cadastrados</span>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Duração</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!count($servicos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                Nenhum serviço cadastrado ainda
                            </td>
                        </tr>
                    <?php else: foreach ($servicos as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['nome']) ?></td>
                            <td><?= htmlspecialchars($s['descricao'] ?? '') ?></td>
                            <td>R$ <?= number_format((float)$s['preco'], 2, ',', '.') ?></td>
                            <td><?= $s['duracao_min'] ? (int)$s['duracao_min'].' min' : '' ?></td>
                            <td><?= htmlspecialchars($s['categoria'] ?? '') ?></td>
                            <td>
                                <form method="POST" action="servicos.php" onsubmit="return confirm('Excluir este serviço?')" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                    <button type="submit" class="logout-btn">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
