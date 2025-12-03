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

// LOG: registra dados recebidos
error_log("add_to_cart DEBUG: produto_nome='$produto_nome', tipo='$tipo', quantidade=$quantidade, preco=$preco, produto_id=$produto_id");

require_once __DIR__ . '/../Model/Connection.php';

try {
    $pdo = Connection::getInstance();
    // força modo de erro por exceção para facilitar debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // se foi passado produto_id, buscar dados do catálogo
    if (!empty($produto_id)) {
        // busca usando os nomes reais das colunas na tabela Produtos
        $pstmt = $pdo->prepare("SELECT nome_produtos AS produto_nome, tipo_produtos AS tipo FROM Produtos WHERE id_produtos = :pid LIMIT 1");
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
        $colStmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Carrinho'");
    $colStmt->execute();
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    // aceitar dois schemas: novo (user_id, produto_nome, tipo, quantidade, preco)
    // ou legado (id_carrinho, id_cliente, id_produtos, quantidade)
    if (empty($cols)) {
        // tenta criar a tabela Carrinho no formato legado (mais compatível com o projeto)
        try {
            $createSql = "CREATE TABLE IF NOT EXISTS Carrinho (
                id_carrinho INT AUTO_INCREMENT PRIMARY KEY,
                id_cliente INT NOT NULL,
                id_produtos INT NOT NULL,
                quantidade INT DEFAULT 1,
                data_adicao DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $pdo->exec($createSql);
            // reconsulta colunas
            $colStmt->execute();
            $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Não foi possível criar/ler a tabela Carrinho.', 'detail' => $e->getMessage()]);
            exit;
        }
    }

    // detecta qual schema está em uso
    $hasNewSchema = in_array('user_id', $cols) && in_array('produto_nome', $cols) && in_array('preco', $cols);
    $hasLegacySchema = in_array('id_cliente', $cols) && in_array('id_produtos', $cols) && in_array('quantidade', $cols);

    if ($hasLegacySchema) {
        // Schema legado: pode receber ou produto_id (preferencial) ou produto_nome+tipo+preco
        if (!empty($produto_id)) {
            // Opção 1: produto_id fornecido, insere direto
            $sql = "INSERT INTO Carrinho (id_cliente, id_produtos, quantidade) VALUES (:cliente, :produto, :qtd)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cliente', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':produto', intval($produto_id), PDO::PARAM_INT);
            $stmt->bindValue(':qtd', intval($quantidade), PDO::PARAM_INT);
            $stmt->execute();
            $insertId = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'id' => $insertId, 'redirect' => 'perfil.php']);
            exit;
        } elseif (!empty($produto_nome)) {
            // Opção 2: buscar produto_id pelo nome_produtos
            try {
                error_log("add_to_cart: buscando produto por nome: '$produto_nome'");
                $pstmt = $pdo->prepare("SELECT id_produtos FROM Produtos WHERE nome_produtos LIKE :nome LIMIT 1");
                $pstmt->bindValue(':nome', '%' . $produto_nome . '%', PDO::PARAM_STR);
                $pstmt->execute();
                $prod = $pstmt->fetch(PDO::FETCH_ASSOC);
                error_log("add_to_cart: resultado da busca: " . json_encode($prod));
                if ($prod) {
                    $found_id = intval($prod['id_produtos']);
                    $sql = "INSERT INTO Carrinho (id_cliente, id_produtos, quantidade) VALUES (:cliente, :produto, :qtd)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':cliente', $userId, PDO::PARAM_INT);
                    $stmt->bindValue(':produto', $found_id, PDO::PARAM_INT);
                    $stmt->bindValue(':qtd', intval($quantidade), PDO::PARAM_INT);
                    $stmt->execute();
                    $insertId = $pdo->lastInsertId();
                    echo json_encode(['success' => true, 'id' => $insertId, 'redirect' => 'perfil.php']);
                    exit;
                }
            } catch (Exception $e) {
                // falha silenciosa, continua abaixo
                error_log("add_to_cart: erro ao buscar produto: " . $e->getMessage());
            }
            // Produto não encontrado pelo nome; redirecionar para novo schema
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Produto '$produto_nome' não encontrado no banco."]);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Produto inválido. Forneça produto_id ou produto_nome.']);
            exit;
        }
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