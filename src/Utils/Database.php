<?php

namespace Utils;

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $host = getenv('MYSQL_HOST') ?: 'mysql';
        $db = getenv('MYSQL_DATABASE') ?: 'phone_shop';
        $user = getenv('MYSQL_USER') ?: 'app_user';
        $pass = getenv('MYSQL_PASSWORD') ?: 'app_password';
        $charset = 'utf8mb4';
        
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    
    // Singleton паттерн для подключения к БД
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Получить PDO объект
    public function getConnection() {
        return $this->pdo;
    }
    
    // Выполнить запрос с параметрами
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Получить одну запись
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    // Получить все записи
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // Получить последний вставленный ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    // Начать транзакцию
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    // Зафиксировать транзакцию
    public function commit() {
        return $this->pdo->commit();
    }
    
    // Откатить транзакцию
    public function rollback() {
        return $this->pdo->rollBack();
    }
}
