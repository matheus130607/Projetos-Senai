<?php
// ATENÇÃO: Ajuste o caminho se necessário (assumindo que esta View está em uma pasta 'view' ou 'public')
require_once __DIR__.'/../controller/ClienteController.php';

// Inicia a sessão para garantir que o Controller possa ser usado.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new ClienteController();
$mensagemErro = null;

// Ação de Cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nome = $_POST['nome_cliente'];
    $cpf = $_POST['cpf_cliente'];
    $cep = $_POST['cep_cliente'];
    $dataNasc = $_POST['data_nasc_cliente'];
    $email = $_POST['email_cliente'];
    $endereco = $_POST['endereco_cliente'];
    $estado = $_POST['estado_cliente'];
    $senha = $_POST['senha_cliente'];

    try {
        $controller->criar($nome, $cpf, $cep, $dataNasc, $email, $endereco, $estado, $senha);
        
        // Redireciona com mensagem de sucesso
        header('Location: ' . $_SERVER['PHP_SELF'] . '?sucesso=1');
        exit;
    } catch (Exception $e) {
        $mensagemErro = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Fit - Cadastro</title>
    <link rel="stylesheet" href="CSS/cadastro.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5" style=" max-width: 400px;">
        <h2 class="title mb-4">Cadastro</h2>
        
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div class="alert alert-success" role="alert">
                ✅ Cadastro realizado com sucesso! Você já pode fazer login.
            </div>
        <?php endif; ?>
        
        <?php if ($mensagemErro): ?>
            <div class="alert alert-danger" role="alert">
                ❌ <?php echo $mensagemErro; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="cxtexto mb-3">
                <label for="nome" class="form-label">Nome completo</label>
                <input type="text" class="form-control" id="nome" name="nome_cliente" placeholder="Digite seu nome" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email_cliente" placeholder="Digite seu e-mail" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf_cliente" placeholder="Digite seu CPF" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="CEP" class="form-label">CEP</label>
                <input type="text" class="form-control" id="CEP" name="cep_cliente" placeholder="Digite seu CEP" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco_cliente" placeholder="Rua, Número e Bairro" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="estado" class="form-label">Estado</label>
                <input type="text" class="form-control" id="estado" name="estado_cliente" placeholder="Digite seu estado (Ex: SP)" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="data_nasc" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="data_nasc" name="data_nasc_cliente" required>
            </div>
            <div class="cxtexto mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha_cliente" placeholder="Digite sua senha" required>
            </div>

            <div class="cxtexto mb-3">
                <label for="termo" class="form-label">Termos de Uso</label>
                <input type="checkbox" name="termo" id="termo" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            <a href="login.php" class="d-block mt-3 text-center">login</a>
        </form>
    </div>
</body>
</html>