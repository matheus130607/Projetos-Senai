<?php
// ATENÇÃO: Ajuste o caminho se necessário
require_once __DIR__ . '/../controller/ClienteController.php';

// Inicia a sessão para armazenar o status de login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new ClienteController();
$mensagemErro = '';

// Lógica de Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $clienteLogado = $controller->login($email, $senha);

    if ($clienteLogado) {
        // Login bem-sucedido
        $_SESSION['user_id'] = $clienteLogado->getId();
        $_SESSION['user_nome'] = $clienteLogado->getNome();
        $_SESSION['user_email'] = $clienteLogado->getEmail();
        $_SESSION['user_perfil'] = $clienteLogado->getPerfilAcesso(); // Salva o perfil
        
        // Redirecionamento baseado no perfil
        if ($_SESSION['user_perfil'] === 'admin') {
            header('Location: adm_clientes.php'); // Próximo arquivo a ser criado
        } else {
            header('Location: painel_cliente.php'); // Página a ser criada para clientes normais
        }
        exit;
    } else {
        $mensagemErro = 'E-mail ou senha inválidos. Tente novamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Fit - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5" style=" max-width: 400px;">
        <h2 class="title mb-4">Acesso ao Cliente</h2>
        
        <?php if ($mensagemErro): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensagemErro; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="acao" value="login">

            <div class="cxtexto mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Seu e-mail cadastrado" required>
            </div>
            
            <div class="cxtexto mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Sua senha" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Entrar</button>
            <a href="cadastro_cliente.php" class="d-block mt-3 text-center">Ainda não tem conta? Cadastre-se</a>
        </form>
    </div>
</body>
</html>