<?php
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'admin') { header('Location: login.php'); exit; }

require_once __DIR__ . '/../controller/AgendamentoController.php';

$controller = new AgendamentoController();
$mensagem = '';
$agParaEdicao = null;

// Função utilitária para exibir alertas na página
function exibirAlerta($msg, $tipo = 'info') {
    if (!$msg) return '';
    $class = $tipo === 'success' ? 'alert-success' : ($tipo === 'warning' ? 'alert-warning' : 'alert-info');
    return "<div class=\"alert $class\">" . htmlspecialchars($msg) . "</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id_agendamento'] ?? null;
    $dataPost = $_POST['data_agendamento'] ?? '';
    $dataFormat = empty($dataPost) ? null : date('Y-m-d H:i:s', strtotime($dataPost));
    try {
        if ($acao === 'salvar') {
            $controller->criar($dataFormat, $_POST['id_funcionario'], $_POST['id_modalidade']);
            $mensagem = 'Sucesso! Agendamento criado.';
        } elseif ($acao === 'editar' && $id) {
            $agParaEdicao = $controller->buscarPorId($id);
        } elseif ($acao === 'atualizar' && $id) {
            $controller->atualizar($id, $dataFormat, $_POST['id_funcionario'], $_POST['id_modalidade']);
            $mensagem = 'Sucesso! Agendamento atualizado.';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($mensagem)); exit;
        } elseif ($acao === 'deletar' && $id) {
            $controller->deletar($id);
            $mensagem = 'Sucesso! Agendamento excluído.';
        }
    } catch (Exception $e) { $mensagem = 'Erro: ' . $e->getMessage(); }
}

$lista = $controller->ler();
$ctrlErr = method_exists($controller,'getLastError') ? $controller->getLastError() : null;
if ($ctrlErr) { $mensagem = $ctrlErr; }
$listaFuncionarios = $controller->listarFuncionariosAdmin();
$listaModalidades = $controller->listarModalidades();

if (isset($_GET['msg'])) $mensagem = htmlspecialchars($_GET['msg']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ADM - Agendamentos</title>
    <link rel="stylesheet" href="CSS/adm.css">
</head>
<body>
<div class="container mt-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1>Painel ADM Tech Fit</h1>
            <a href="logout.php" class="btn btn-danger">Sair / Logout</a>
        </div>

        <nav class="nav nav-tabs mb-4">
            <a class="nav-link" href="adm_clientes.php">Clientes</a>
            <a class="nav-link" href="adm_produtos.php">Produtos</a>
            <a class="nav-link" href="adm_modalidades.php">Modalidades</a>
            <a class="nav-link active" aria-current="page" href="adm_agendamentos.php">Agendamentos</a>
            <a class="nav-link" href="adm_franquias.php">Franquias</a>
        </nav>

        <?php
            $isEdicao = $agParaEdicao !== null;
            $formTitle = $isEdicao ? "Editando Agendamento ID: " . $agParaEdicao->getId() : "Cadastrar Novo Agendamento";
            $acaoForm = $isEdicao ? 'atualizar' : 'salvar';
        ?>
        <h2><?php echo $formTitle; ?></h2>
    
        <?php echo exibirAlerta($mensagem, strpos($mensagem,'Sucesso')!==false? 'success':'warning'); ?>
    <form method="POST" class="p-3 mb-4 border rounded bg-light">
        <input type="hidden" name="acao" value="<?php echo $acaoForm; ?>">
        <?php if ($isEdicao): ?><input type="hidden" name="id_agendamento" value="<?php echo $agParaEdicao->getId(); ?>"><?php endif; ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="data" class="form-label">Data e Hora:</label>
                <input type="datetime-local" id="data" name="data_agendamento" class="form-control"
                    value="<?php echo $isEdicao && $agParaEdicao->getData() ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($agParaEdicao->getData()))) : ''; ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="func" class="form-label">Funcionário/Admin:</label>
                <select id="func" name="id_funcionario" class="form-select" required>
                    <option value="">-- Selecionar --</option>
                    <?php foreach ($listaFuncionarios as $f): ?>
                        <option value="<?php echo $f->getId(); ?>" <?php echo ($isEdicao && $agParaEdicao->getIdFuncionario()==$f->getId())? 'selected':''; ?>><?php echo htmlspecialchars($f->getNome()); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="mod" class="form-label">Modalidade:</label>
                <select id="mod" name="id_modalidade" class="form-select" required>
                    <option value="">-- Selecionar --</option>
                    <?php foreach ($listaModalidades as $m): ?>
                        <option value="<?php echo $m->getId(); ?>" <?php echo ($isEdicao && $agParaEdicao->getIdModalidade()==$m->getId())? 'selected':''; ?>><?php echo htmlspecialchars($m->getNome()); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-<?php echo $isEdicao? 'success':'primary'; ?> me-2"><?php echo $isEdicao? 'Salvar':'Cadastrar'; ?></button>
        <?php if ($isEdicao): ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar</a><?php endif; ?>
    </form>

    <div class="mb-3"><input type="text" id="searchAg" class="form-control" placeholder="Pesquisar por funcionário ou modalidade..." onkeyup="filterAgendamentos(this.value)"></div>
    <table class="table table-striped table-hover" id="agTable">
        <thead class="table-dark"><tr><th>ID</th><th>Data</th><th>Funcionário</th><th>Modalidade</th><th style="width:150px;">Ações</th></tr></thead>
        <tbody>
        <?php if (empty($lista)): ?>
            <tr><td colspan="5" class="text-center">Nenhum agendamento.</td></tr>
        <?php else: foreach ($lista as $a): ?>
            <tr>
                <td><?php echo $a->getId(); ?></td>
                <td><?php echo $a->getData() ? date('d/m/Y H:i', strtotime($a->getData())) : 'N/A'; ?></td>
                <td><?php echo htmlspecialchars($a->nome_funcionario ?? '---'); ?></td>
                <td><?php echo htmlspecialchars($a->nome_modalidade ?? '---'); ?></td>
                <td class="acoes">
                    <form method="POST" style="display:inline-block;margin-right:6px;">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id_agendamento" value="<?php echo $a->getId(); ?>">
                        <button class="btn btn-sm btn-primary">Editar</button>
                    </form>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Excluir agendamento?');">
                        <input type="hidden" name="acao" value="deletar">
                        <input type="hidden" name="id_agendamento" value="<?php echo $a->getId(); ?>">
                        <button class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<script>
function filterAgendamentos(q) {
    const ql = q.trim().toLowerCase();
    const rows = document.getElementById('agTable').tBodies[0].rows;
    for (let r of rows) {
        const nome = (r.cells[2].textContent||'').toLowerCase();
        const mod = (r.cells[3].textContent||'').toLowerCase();
        r.style.display = (nome.indexOf(ql)>-1 || mod.indexOf(ql)>-1) ? '' : 'none';
    }
}
</script>
</body>
</html>
