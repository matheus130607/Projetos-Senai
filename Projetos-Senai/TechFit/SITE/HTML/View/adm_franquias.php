<?php
date_default_timezone_set('America/Sao_Paulo');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'admin') { header('Location: login.php'); exit; }

require_once __DIR__ . '/../controller/FranquiaController.php';

$ctrl = new FranquiaController();
$mensagem = '';
$frParaEd = null;

// Função utilitária para exibir alertas na página
function exibirAlerta($msg, $tipo = 'info') {
    if (!$msg) return '';
    $class = $tipo === 'success' ? 'alert-success' : ($tipo === 'warning' ? 'alert-warning' : 'alert-info');
    return "<div class=\"alert $class\">" . htmlspecialchars($msg) . "</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id_franquia'] ?? null;
    try {
        if ($acao === 'salvar') {
            $ctrl->criar($_POST['nome'], $_POST['descricao'], $_POST['endereco'], $_POST['cidade'], $_POST['estado'], $_POST['telefone']);
            $mensagem = 'Sucesso! Franquia criada.';
        } elseif ($acao === 'editar' && $id) {
            $frParaEd = $ctrl->buscarPorId($id);
        } elseif ($acao === 'atualizar' && $id) {
            $ctrl->atualizar($id, $_POST['nome'], $_POST['descricao'], $_POST['endereco'], $_POST['cidade'], $_POST['estado'], $_POST['telefone']);
            $mensagem = 'Sucesso! Franquia atualizada.';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($mensagem)); exit;
        } elseif ($acao === 'deletar' && $id) {
            $ctrl->deletar($id);
            $mensagem = 'Sucesso! Franquia excluída.';
        }
    } catch (Exception $e) { $mensagem = 'Erro: ' . $e->getMessage(); }
}

$lista = $ctrl->ler();
$ctrlErr = method_exists($ctrl, 'getLastError') ? $ctrl->getLastError() : null;
if ($ctrlErr) {
    $mensagem = $ctrlErr;
}
if (isset($_GET['msg'])) $mensagem = htmlspecialchars($_GET['msg']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ADM - Franquias</title>
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
            <a class="nav-link" href="adm_agendamentos.php">Agendamentos</a>
            <a class="nav-link active" aria-current="page"  href="adm_franquias.php">Franquias</a>
        </nav>
    <?php echo exibirAlerta($mensagem, strpos($mensagem,'Sucesso')!==false? 'success':'warning'); ?>

    <?php
        $isEdicao = $frParaEd !== null;
        $formTitle = $isEdicao ? "Editando Franquia ID: " . $frParaEd->getId() : "Cadastrar Nova Franquia";
        $acaoForm = $isEdicao ? 'atualizar' : 'salvar';
    ?>
    <h2><?php echo $formTitle; ?></h2>
    <form method="POST" class="p-3 mb-4 border rounded bg-light">
        <input type="hidden" name="acao" value="<?php echo $acaoForm; ?>">
        <?php if ($isEdicao): ?><input type="hidden" name="id_franquia" value="<?php echo $frParaEd->getId(); ?>"><?php endif; ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" placeholder="Nome da franquia" required value="<?php echo $isEdicao? htmlspecialchars($frParaEd->getNome()):''; ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" placeholder="Cidade" value="<?php echo $isEdicao? htmlspecialchars($frParaEd->getCidade()):''; ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" placeholder="Rua, número, bairro" value="<?php echo $isEdicao? htmlspecialchars($frParaEd->getEndereco()):''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Estado</label>
                <input type="text" name="estado" class="form-control" maxlength="2" placeholder="SP" value="<?php echo $isEdicao? htmlspecialchars($frParaEd->getEstado()):'SP'; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" placeholder="(19) 9 9999-9999" value="<?php echo $isEdicao? htmlspecialchars($frParaEd->getTelefone()):''; ?>">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" placeholder="Descrição da unidade" style="resize:none;" rows="3"><?php echo $isEdicao? htmlspecialchars($frParaEd->getDescricao()):''; ?></textarea>
            </div>
            <!-- campo E-mail removido conforme solicitado -->
        </div>
        <button class="btn btn-<?php echo $isEdicao? 'success':'primary'; ?> me-2"><?php echo $isEdicao? 'Salvar Alterações':'Criar'; ?></button>
        <?php if ($isEdicao): ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar</a><?php endif; ?>
    </form>

    <div class="mb-3"><input type="text" id="searchFr" class="form-control" placeholder="Pesquisar por nome..." onkeyup="filterFranquias(this.value)"></div>

    <table class="table table-striped table-hover" id="frTable">
        <thead class="table-dark"><tr><th>ID</th><th>Nome</th><th>Cidade</th><th>Telefone</th><th style="width:150px;">Ações</th></tr></thead>
        <tbody>
        <?php if (empty($lista)): ?>
            <tr><td colspan="5" class="text-center">Nenhuma franquia cadastrada.</td></tr>
        <?php else: foreach ($lista as $f): ?>
            <tr>
                <td><?php echo $f->getId(); ?></td>
                <td><?php echo htmlspecialchars($f->getNome()); ?></td>
                <td><?php echo htmlspecialchars($f->getCidade()); ?></td>
                <td><?php echo htmlspecialchars($f->getTelefone()); ?></td>
                <td class="acoes">
                    <form method="POST" style="display:inline-block;margin-right:6px;">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id_franquia" value="<?php echo $f->getId(); ?>">
                        <button class="btn btn-sm btn-primary">Editar</button>
                    </form>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Excluir franquia?');">
                        <input type="hidden" name="acao" value="deletar">
                        <input type="hidden" name="id_franquia" value="<?php echo $f->getId(); ?>">
                        <button class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<script>
function filterFranquias(q) {
    const ql = q.trim().toLowerCase();
    const rows = document.getElementById('frTable').tBodies[0].rows;
    for (let r of rows) {
        const nome = (r.cells[1].textContent||'').toLowerCase();
        r.style.display = nome.indexOf(ql)>-1 ? '' : 'none';
    }
}
</script>
</body>
</html>
