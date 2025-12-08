<?php
require_once __DIR__.'/../controller/ClienteController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new ClienteController();
$mensagemErro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome_cliente'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf_cliente']);
    $cep = preg_replace('/\D/', '', $_POST['cep_cliente']); // remove não dígitos
    $cep = substr($cep, 0, 8); // garante no máximo 8 dígitos
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
    <link rel="stylesheet" href="CSS/login_register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="JS/toggle-password.js"></script>
</head>

<body>

    <h1 style="cursor: pointer;" onclick="window.location.href='Pag_Inicial_CL.html'">TECHFIT</h1>

    <div class="container-cadastro">
        <h2 class="title">Cadastro</h2>
        
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div class="alert-success">Cadastro realizado com sucesso! Você já pode fazer login.</div>
        <?php endif; ?>
        
        <?php if ($mensagemErro): ?>
            <div class="alert-danger"><?php echo $mensagemErro; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validarFormulario()">
            
            <div class="form-grid">

                <!-- ESQUERDA: Nome -->
                <div class="cxtexto mb-3 col-left">
                    <label for="nome">Nome completo</label>
                    <input type="text" class="form-control" id="nome" name="nome_cliente" placeholder="Digite seu nome" required>
                </div>

                <!-- DIREITA: CEP -->
                <div class="cxtexto mb-3 col-right">
                    <label for="cep">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep_cliente" placeholder="Digite seu CEP" maxlength="8"
       oninput="this.value = this.value.replace(/\D/g,'').slice(0,8)" required>
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
                    <input type="text" class="form-control" id="cpf" name="cpf_cliente" placeholder="Digite seu CPF" maxlength="14" oninput="this.value = formatCPF(this.value)" required>
                </div>

                <!-- DIREITA: Estado -->
                <div class="cxtexto mb-3 col-right">
                    <label for="estado">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado_cliente" placeholder="Ex: SP" required>
                </div>

                <!-- ESQUERDA: Senha -->
                <div class="cxtexto mb-3 col-left">
                    <label for="senha">Senha</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="senha" name="senha_cliente" placeholder="Digite sua senha" required>
                        <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('senha')"></i>
                    </div>
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
                    <small id="erroIdade" style="color: #ff6b6b; display: none;">Você deve ter pelo menos 16 anos.</small>
                </div>

                <!-- DIREITA: Botão -->
                <div class="cxtexto mb-3 col-right" style="align-self:start;">
                    <button type="submit" class="btn-cadastro">Cadastrar</button>
                </div>

            </div>

            <a href="login.php" class="link-cadastro">Login</a>

        </form>
    </div>

    <script>
    function formatCPF(value) {
        value = value.replace(/\D/g, '').slice(0,11);
        if (value.length > 9) {
            return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            return value.replace(/^(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
        } else if (value.length > 3) {
            return value.replace(/^(\d{3})(\d{0,3})/, '$1.$2');
        }
        return value;
    }

    function validarIdade(dataNascimento) {
        const hoje = new Date();
        const dataNasc = new Date(dataNascimento);
        let idade = hoje.getFullYear() - dataNasc.getFullYear();
        const mesAtual = hoje.getMonth();
        const mesNasc = dataNasc.getMonth();

        if (mesAtual < mesNasc || (mesAtual === mesNasc && hoje.getDate() < dataNasc.getDate())) {
            idade--;
        }

        return idade >= 16;
    }

    function validarFormulario() {
        const dataNasc = document.getElementById('data_nasc').value;
        const erroIdade = document.getElementById('erroIdade');

        if (!dataNasc) {
            erroIdade.style.display = 'none';
            return true;
        }

        if (!validarIdade(dataNasc)) {
            erroIdade.style.display = 'block';
            return false;
        }

        erroIdade.style.display = 'none';
        return true;
    }

    // Validar quando mudar a data
    document.getElementById('data_nasc').addEventListener('change', function() {
        validarFormulario();
    });
    </script>
</body>
</html>
