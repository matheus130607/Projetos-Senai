<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.', 'login_required' => true]);
    exit;
}

$userId = intval($_SESSION['user_id']);
$produto_id = isset($_POST['produto_id']) ? (is_numeric($_POST['produto_id']) ? intval($_POST['produto_id']) : null) : null;
$produto_nome = trim($_POST['produto_nome'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$quantidade = intval($_POST['quantidade'] ?? 1);
$preco_raw = trim($_POST['preco'] ?? '0');

if ($produto_nome === '' && empty($produto_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Produto inválido. Forneça nome ou produto_id.']);
    exit;
}

if ($quantidade <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Quantidade inválida.']);
    exit;
}

function parse_price(string $raw): float {
    $s = trim(str_replace(['R$', ' '], '', $raw));
    if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
        // ponto = milhares, vírgula = decimal
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    } elseif (strpos($s, ',') !== false && strpos($s, '.') === false) {
        $s = str_replace(',', '.', $s);
    }
    $s = preg_replace('/[^\d\.]/', '', $s);
    return $s === '' ? 0.0 : floatval($s);
}

$preco = parse_price($preco_raw);

require_once __DIR__ . '/../Model/Connection.php';

try {
    $pdo = Connection::getInstance();
    // força modo de erro por exceção para facilitar debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // se foi passado produto_id, buscar dados do catálogo
    if (!empty($produto_id)) {
        // ajuste o nome da tabela/colunas caso necessário
        $pstmt = $pdo->prepare("SELECT nome AS produto_nome, tipo, preco FROM Produtos WHERE id = :pid LIMIT 1");
        $pstmt->bindValue(':pid', $produto_id, PDO::PARAM_INT);
        $pstmt->execute();
        $prod = $pstmt->fetch(PDO::FETCH_ASSOC);
        if (!$prod) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
            exit;
        }
        $produto_nome = $prod['produto_nome'] ?? $produto_nome;
        $tipo = $prod['tipo'] ?? $tipo;
        $preco = parse_price((string)($prod['preco'] ?? $preco));
    }

    // Verifica colunas existentes na tabela Carrinho
    $colStmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Carrinho'
    ");
    $colStmt->execute();
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!$cols || !in_array('user_id', $cols) || !in_array('produto_nome', $cols) || !in_array('quantidade', $cols) || !in_array('preco', $cols)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Estrutura da tabela Carrinho não é compatível. Colunas obrigatórias faltando.']);
        exit;
    }

    $hasProdutoIdCol = in_array('produto_id', $cols);

    // Monta INSERT dinamicamente conforme colunas disponíveis
    $fields = ['user_id', 'produto_nome', 'tipo', 'quantidade', 'preco'];
    $placeholders = [':uid', ':nome', ':tipo', ':qtd', ':preco'];
    $binds = [
        ':uid' => [$userId, PDO::PARAM_INT],
        ':nome' => [$produto_nome, PDO::PARAM_STR],
        ':tipo' => [$tipo, PDO::PARAM_STR],
        ':qtd' => [$quantidade, PDO::PARAM_INT],
        ':preco' => [$preco, PDO::PARAM_STR], // armazenar como string/decimal
    ];

    if ($hasProdutoIdCol) {
        array_splice($fields, 1, 0, 'produto_id'); // insere produto_id após user_id
        array_splice($placeholders, 1, 0, ':pid');
        // bind para produto_id: se vazio usar NULL
        if (!empty($produto_id)) {
            $binds[':pid'] = [$produto_id, PDO::PARAM_INT];
        } else {
            $binds[':pid'] = [null, PDO::PARAM_NULL];
        }
    }

    $sql = "INSERT INTO Carrinho (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);

    // vincula valores com tipos apropriados
    foreach ($binds as $param => [$value, $type]) {
        if ($type === PDO::PARAM_NULL) {
            $stmt->bindValue($param, null, PDO::PARAM_NULL);
        } elseif ($type === PDO::PARAM_INT) {
            $stmt->bindValue($param, intval($value), PDO::PARAM_INT);
        } else {
            $stmt->bindValue($param, (string)$value, PDO::PARAM_STR);
        }
    }

    $stmt->execute();
    $insertId = $pdo->lastInsertId();

    echo json_encode(['success' => true, 'id' => $insertId, 'redirect' => 'perfil.php']);
} catch (Exception $e) {
    // log detalhado no servidor
    error_log('add_to_cart error: ' . $e->getMessage() . ' -- SQL: ' . ($sql ?? 'n/a'));
    http_response_code(500);
    // retornar mensagem curta para ajudar debugging local
    echo json_encode(['success' => false, 'error' => 'Erro ao inserir no carrinho.', 'detail' => $e->getMessage()]);
}
?>