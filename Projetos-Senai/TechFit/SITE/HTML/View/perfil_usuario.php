<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechFit - Meu Perfil</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <header class="header">
    <h1 class="titulo-techfit" onclick="home()">TECHFIT</h1>
    <button onclick="franquias()" id="franquias">Franquias</button>
    <button onclick="plano()" id="planos">Planos</button>
    <button onclick="faleconosco()" id="faleconosco">Fale Conosco</button>
    <button onclick="modalidades()" id="modalidades">Modalidades</button>
    <button onclick="loja()" id="loja">Loja</button>
    <div class="dropdown usuario-dropdown">
        <img src="IMG/usuario.png" class="dropbtn usuario-btn" alt="Usuário">
        <div class="dropdown-content">
            <a href="perfil_usuario.php"><i class="bi bi-person"></i> Perfil</a>
            <a href="login.html"><i class="bi bi-house"></i> Login</a>
        </div>
    </div>
  </header>

  <main>
    <div class="container">
      
      <section class="secao" style="padding-bottom: 0;">
        <h2 style="font-size: 3rem; text-align: left;">Minha Conta</h2>
        <p class="text-white-50" style="text-align: left;">Olá, Matheus! (Nome fictício)</p>
      </section>

      <div class="row mt-4">
        
        <div class="col-lg-3 col-md-4">
          <div class="nav flex-column nav-pills" id="perfilTabs" role="tablist" aria-orientation="vertical">
            
            <button class="nav-link active" id="agendamentos-tab" data-bs-toggle="pill" data-bs-target="#agendamentos-pane" type="button" role="tab" aria-controls="agendamentos-pane" aria-selected="true">
              <i class="bi bi-calendar-check-fill me-2"></i> Meus Agendamentos
            </button>
            
            <button class="nav-link" id="carrinho-tab" data-bs-toggle="pill" data-bs-target="#carrinho-pane" type="button" role="tab" aria-controls="carrinho-pane" aria-selected="false">
              <i class="bi bi-cart-fill me-2"></i> Carrinho de Compras
            </button>
            
            <button class="nav-link" id="historico-tab" data-bs-toggle="pill" data-bs-target="#historico-pane" type="button" role="tab" aria-controls="historico-pane" aria-selected="false">
              <i class="bi bi-receipt-cutoff me-2"></i> Histórico de Compras
            </button>
          </div>
        </div>

        <div class="col-lg-9 col-md-8">
  <div class="tab-content" id="perfilTabsContent">

    <div class="tab-pane fade show active" id="agendamentos-pane" role="tabpanel" aria-labelledby="agendamentos-tab" tabindex="0">
      <h3>Próximas Aulas</h3>
      <div class="agendamento-card">
        <div class="agendamento-card-info">
          </div>
      </div>
    </div> <div class="tab-pane fade" id="carrinho-pane" role="tabpanel" aria-labelledby="carrinho-tab" tabindex="0">
<div class="container mt-5">
    <h3>Meu Carrinho de Compras</h3>
    <table class="table table-striped" id="tabela-carrinho">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
<?php
session_start();

// se sua autenticação guarda o id do usuário em $_SESSION['user_id']
if (empty($_SESSION['user_id'])) {
    // redirecionar para login ou mostrar mensagem
    // header('Location: login.html'); exit;
    $userId = null; // deixa nulo para não mostrar nada
} else {
    $userId = intval($_SESSION['user_id']);
}

require_once __DIR__ . '/../Model/Connection.php';

$cartItems = [];
if ($userId) {
    try {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT produto_nome, tipo, quantidade, preco FROM Carrinho WHERE user_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $userId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // logar se desejar: error_log($e->getMessage());
        $cartItems = [];
    }
}
foreach ($cartItems as $item): ?>
    <tr>
        <td><?php echo htmlspecialchars($item['produto_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($item['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo intval($item['quantidade']); ?></td>
    </tr>
<?php endforeach; ?>
        </tbody>
    </table>
</div> <div class="tab-pane fade" id="historico-pane" role="tabpanel" aria-labelledby="historico-tab" tabindex="0">
      <h3>Histórico de Pedidos</h3>
      <div class="accordion" id="accordionHistorico">
        </div>
    </div> </div> </div>
  </main>

  <footer class="footer">
    <div class="footer-left">
        <h2>TECHFIT</h2>
        <div class="links-footer" id="links-footer">
            <a href="https://github.com/matheus130607" target="_blank" rel="noopener noreferrer">
                <p class="links"><i class="bi bi-github"></i></p>
            </a>
            <a href="https://www.whatsapp.com/?lang=pt" target="_blank" rel="noopener noreferrer">
                <p class="links"><i class="bi bi-whatsapp"></i></p>
            </a>
            <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">
                <p class="links"><i class="bi bi-instagram"></i></p>
            </a>
            <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer">
                <p class="links"><i class="bi bi-linkedin"></i></p>
            </a>
            <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">
                <p class="links"><i class="bi bi-facebook"></i></p>
            </a>
        </div>
    </div>
    <div class="footer-right">
        <p class="link" onclick="">Sobre Nós &#8599;</p>
        <p class="link" onclick="">Telefone &#8599;</p>
        <p class="link" onclick="">Franquias &#8599;</p>
    </div>
    <p class="direitos">&copy; Todos os direitos reservados TechFit</p>
  </footer>

  <script src="JS/index.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>