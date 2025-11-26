<?php
require_once __DIR__.'/../controller/ClienteController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new ClienteController();
$mensagemErro = null;

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
    <link rel="stylesheet" href="login_register.css">
</head>

<body>

    <h1>TechFit</h1>

    <div class="container-cadastro">
        <h2 class="title">Cadastro</h2>
        
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div class="alert-success">Cadastro realizado com sucesso! Você já pode fazer login.</div>
        <?php endif; ?>
        
        <?php if ($mensagemErro): ?>
            <div class="alert-danger"><?php echo $mensagemErro; ?></div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="form-grid">

                <!-- ESQUERDA: Nome -->
                <div class="cxtexto mb-3 col-left">
                    <label for="nome">Nome completo</label>
                    <input type="text" class="form-control" id="nome" name="nome_cliente" placeholder="Digite seu nome" required>
                </div>

                <!-- DIREITA: CEP -->
                <div class="cxtexto mb-3 col-right">
                    <label for="cep">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep_cliente" placeholder="Digite seu CEP" required>
                </div>

                <!-- ESQUERDA: Email -->
                <div class="cxtexto mb-3 col-left">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email_cliente" placeholder="Digite seu e-mail" required>
                </div>

                <!-- DIREITA: Endereço -->
                <div class="cxtexto mb-3 col-right">
                    <label for="endereco">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco_cliente" placeholder="Rua, Número e Bairro" required>
                </div>

                <!-- ESQUERDA: CPF -->
                <div class="cxtexto mb-3 col-left">
                    <label for="cpf">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf_cliente" placeholder="Digite seu CPF" required>
                </div>

                <!-- DIREITA: Estado -->
                <div class="cxtexto mb-3 col-right">
                    <label for="estado">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado_cliente" placeholder="Ex: SP" required>
                </div>

                <!-- ESQUERDA: Senha -->
                <div class="cxtexto mb-3 col-left">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha_cliente" placeholder="Digite sua senha" required>
                </div>

                <!-- DIREITA: Termos -->
                <div class="cxtexto mb-3 col-right">
                    <label for="termo">Termos de Uso</label>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:6px;">
                        <input type="checkbox" id="termo" name="termo" required>
                        <label for="termo" style="color:#d6d6d6; margin:0;">Aceito os termos de uso</label>
                    </div>
                </div>

                <!-- ESQUERDA: Data de Nascimento -->
                <div class="cxtexto mb-3 col-left">
                    <label for="data_nasc">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nasc" name="data_nasc_cliente" required>
                </div>

                <!-- DIREITA: Botão -->
                <div class="cxtexto mb-3 col-right" style="align-self:start;">
                    <button type="submit" class="btn-cadastro">Cadastrar</button>
                </div>

            </div>

            <a href="login.php" class="link-cadastro">Login</a>

        </form>
    </div>

</body>
</html>
