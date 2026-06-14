<?php
class DB {
    public static function conectar() {
        $host = 'localhost';
        $dbname = 'db_pweb1_elvi';
        $user = 'root';
        $pass = ''; // Senha padrão do Laragon é vazia

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }
}
?>