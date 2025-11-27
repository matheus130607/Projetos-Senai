<?php
// Define o fuso horário para consistência (opcional, mas recomendado)
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Controle de Acesso
// Redireciona se o usuário não estiver logado ou não for admin
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../controller/ClienteController.php';

$controller = new ClienteController();
$mensagem = '';
$clienteParaEdicao = null;

// --- 2. Lógica de Ações (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id_cliente'] ?? null;
    
    // CORREÇÃO DO ERRO DEPRECATED (LINHAS 29-31):
    // 1. Garante que $dataNascPost é uma string vazia se não for postada.
    $dataNascPost = $_POST['data_nasc_cliente'] ?? '';
    
    // 2. Converte a data APENAS se a string não estiver vazia, caso contrário é null.
    $dataNascFormatada = empty($dataNascPost) ? null : date('Y-m-d', strtotime($dataNascPost));

    try {
        if ($acao === 'salvar') {
            // Ação de CRIAÇÃO/INSERÇÃO - O perfil será definido no Controller
            $controller->criar(
                $_POST['nome_cliente'],
                $_POST['CPF_cliente'],
                $_POST['CEP_cliente'],
                $dataNascFormatada, // Usa o valor corrigido
                $_POST['email_cliente'],
                $_POST['endereco_cliente'],
                $_POST['estado_cliente'],
                $_POST['senha_cliente']
            );
            $mensagem = "Sucesso! Cliente '{$_POST['nome_cliente']}' cadastrado.";

        } elseif ($acao === 'deletar' && $id) {
            // Ação de EXCLUSÃO
            $controller->deletar($id);
            $mensagem = "Sucesso! Cliente ID $id excluído.";
            
        } elseif ($acao === 'editar' && $id) {
            // Ação de PREPARAR FORMULÁRIO para edição
            $clienteParaEdicao = $controller->buscarPorId($id);
            if (!$clienteParaEdicao) {
                $mensagem = "Erro: Cliente não encontrado para edição.";
            }

        } elseif ($acao === 'atualizar' && $id) {
            // Ação de ATUALIZAR os dados (Após o formulário de edição)
            $controller->atualizar(
                $id,
                $_POST['nome_cliente'],
                $_POST['CPF_cliente'],
                $_POST['CEP_cliente'],
                $dataNascFormatada, // Usa o valor corrigido
                $_POST['email_cliente'],
                $_POST['endereco_cliente'],
                $_POST['estado_cliente'],
                $_POST['senha_cliente'],
                $_POST['perfil_acesso'] // O perfil é atualizado aqui!
            );
            $mensagem = "Sucesso! Cliente ID $id atualizado.";
            // Redireciona para limpar o POST e a URL
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($mensagem));
            exit;
        }
    } catch (Exception $e) {
        $mensagem = "Erro ao processar ação: " . $e->getMessage();
    }
}

// Carrega a lista de todos os clientes
$listaTotal = $controller->ler();
$listaFuncionarios = [];
$listaClientes = [];

// Separa a lista em funcionários e clientes comuns
foreach ($listaTotal as $cliente) {
    if ($cliente->getPerfilAcesso() === 'admin') {
        $listaFuncionarios[] = $cliente;
    } else {
        $listaClientes[] = $cliente;
    }
}
// Ordena as listas por ID (menor -> maior)
if (is_array($listaFuncionarios)) {
    usort($listaFuncionarios, function($a, $b) { return $a->getId() <=> $b->getId(); });
}
if (is_array($listaClientes)) {
    usort($listaClientes, function($a, $b) { return $a->getId() <=> $b->getId(); });
}

// Verifica se há mensagem de redirecionamento na URL
if (isset($_GET['msg'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADM Tech Fit - Gerenciamento de Clientes</title>
    <link rel="stylesheet" href="CSS/adm.css">
</head>
<body>
    <div class="container mt-5">
        
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1>Painel ADM Tech Fit</h1>
            <a href="logout.php" class="btn btn-danger">Sair / Logout</a>
        </div>

        <nav class="nav nav-tabs mb-4">
            <a class="nav-link active" aria-current="page" href="adm_clientes.php">Clientes</a>
            <a class="nav-link" href="adm_produtos.php">Produtos</a>
            <a class="nav-link" href="adm_modalidades.php">Modalidades</a>
            <a class="nav-link disabled" href="#">Agendamentos (Em Breve)</a>
            <a class="nav-link disabled" href="#">Franquias (Em Breve)</a>
        </nav>
        <p>Logado como: <strong><?php echo htmlspecialchars($_SESSION['user_nome'] ?? 'Admin'); ?></strong> (Perfil: <?php echo $_SESSION['user_perfil']; ?>)</p>
        
        <?php if ($mensagem): ?>
            <div class="alert <?php echo strpos($mensagem, 'Sucesso!') !== false ? 'alert-success' : 'alert-warning'; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <?php 
        // Determina se é um formulário de EDIÇÃO ou CADASTRO
        $isEdicao = $clienteParaEdicao !== null;
        $formTitle = $isEdicao ? "Editando Cliente ID: " . $clienteParaEdicao->getId() : "Cadastrar Novo Cliente/Funcionário";
        $acaoForm = $isEdicao ? 'atualizar' : 'salvar';
        ?>

        <hr>
        <h2><?php echo $formTitle; ?></h2>
        <form method="POST" class="p-3 mb-4 border rounded bg-light">
            <input type="hidden" name="acao" value="<?php echo $acaoForm; ?>">
            <?php if ($isEdicao): ?>
                <input type="hidden" name="id_cliente" value="<?php echo $clienteParaEdicao->getId(); ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome Completo:</label>
                    <input type="text" class="form-control" id="nome" name="nome_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getNome()) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail (login):</label>
                    <input type="email" class="form-control" id="email" name="email_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getEmail()) : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cpf" class="form-label">CPF:</label>
                    <input type="text" class="form-control" id="cpf" name="CPF_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getCPF()) : ''; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="dataNasc" class="form-label">Data de Nascimento:</label>
                    <input type="date" class="form-control" id="dataNasc" name="data_nasc_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getDataNasc()) : ''; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getSenha()) : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="endereco" class="form-label">Endereço:</label>
                    <input type="text" class="form-control" id="endereco" name="endereco_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getEndereco()) : ''; ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cep" class="form-label">CEP:</label>
                    <input type="text" class="form-control" id="cep" name="CEP_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getCEP()) : ''; ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="estado" class="form-label">Estado (UF):</label>
                    <input type="text" class="form-control" id="estado" name="estado_cliente" 
                           value="<?php echo $isEdicao ? htmlspecialchars($clienteParaEdicao->getEstado()) : ''; ?>" maxlength="2" required>
                </div>
            </div>

            <?php if ($isEdicao): ?>
                <div class="mb-3">
                    <label for="perfil" class="form-label">Perfil de Acesso:</label>
                    <select class="form-select" id="perfil" name="perfil_acesso" required>
                        <option value="cliente" <?php echo $clienteParaEdicao->getPerfilAcesso() === 'cliente' ? 'selected' : ''; ?>>Cliente Comum</option>
                        <option value="admin" <?php echo $clienteParaEdicao->getPerfilAcesso() === 'admin' ? 'selected' : ''; ?>>Funcionário/Admin</option>
                    </select>
                    <small class="form-text text-muted">Atenção: Apenas perfis 'Admin' têm acesso a este painel.</small>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-<?php echo $isEdicao ? 'success' : 'primary'; ?> me-2">
                <?php echo $isEdicao ? 'Salvar Alterações' : 'Cadastrar'; ?>
            </button>
            <?php if ($isEdicao): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancelar Edição</a>
            <?php endif; ?>
        </form>
        <hr>

        <h2>Funcionários e Administradores (Total: <?php echo count($listaFuncionarios); ?>)</h2>
        <div class="mb-3">
            <input type="text" id="searchFuncionarios" class="form-control" placeholder="Pesquisar por nome..." onkeyup="filterTableByName('funcionariosTable', 1, this.value)">
        </div>
        <table id="funcionariosTable" class="table table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Perfil</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaFuncionarios)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum funcionário cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaFuncionarios as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente->getId(); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getNome()); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getEmail()); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getCPF()); ?></td>
                        <td><span class="badge bg-warning"><?php echo ucfirst($cliente->getPerfilAcesso()); ?></span></td>
                        <td class="acoes">
                            <form method="POST">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Editar</button>
                            </form>
                            
                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o funcionário: <?php echo htmlspecialchars($cliente->getNome()); ?>?');">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <hr class="mt-5 mb-5">

        <h2>Clientes Comuns (Total: <?php echo count($listaClientes); ?>)</h2>
        <div class="mb-3">
            <input type="text" id="searchClientes" class="form-control" placeholder="Pesquisar por nome..." onkeyup="filterTableByName('clientesTable', 1, this.value)">
        </div>
        <table id="clientesTable" class="table table-striped table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Data Nasc.</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaClientes)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum cliente comum cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listaClientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente->getId(); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getNome()); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getEmail()); ?></td>
                        <td><?php echo htmlspecialchars($cliente->getCPF()); ?></td>
                        <td><?php echo $cliente->getDataNasc() ? date('d/m/Y', strtotime($cliente->getDataNasc())) : 'N/A'; ?></td>
                        <td class="acoes">
                            <form method="POST">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Editar</button>
                            </form>
                            
                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o cliente: <?php echo htmlspecialchars($cliente->getNome()); ?>?');">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente->getId(); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        function filterTableByName(tableId, nameColIndex, query) {
            const filter = query.trim().toLowerCase();
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = table.tBodies[0].rows;
            for (let i = 0; i < rows.length; i++) {
                const cell = rows[i].cells[nameColIndex];
                if (!cell) continue;
                const text = cell.textContent || cell.innerText;
                rows[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    </script>
</body>
</html>