<?php

class Connection {

    private static $instance = null;

    public static function getInstance() {
        if (!self::$instance) {
            try {
                // --- Configurações de Conexão ---
                $host = 'localhost';
                $dbname = 'Techfit'; // Nome do seu banco de dados
                $user = 'root';
                $pass = 'senaisp'; // ATENÇÃO: Use a senha correta do seu MySQL.
                
                $dsn_server = "mysql:host=$host;charset=utf8";

                self::$instance = new PDO(
                    $dsn_server,
                    $user,
                    $pass
                );
                
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 2. CRIAÇÃO E SELEÇÃO DO BANCO DE DADOS:
                self::$instance->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$instance->exec("USE $dbname");
                
                // 3. ADICIONA COLUNA DE PERFIL SE NÃO EXISTIR (Execute isso manualmente se houver erro no DAO)
                // self::$instance->exec("
                //     ALTER TABLE Clientes 
                //     ADD COLUMN IF NOT EXISTS perfil_acesso VARCHAR(10) NOT NULL DEFAULT 'cliente'
                // ");

            } catch (PDOException $e) {
                die("Erro ao conectar ao MySQL: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>