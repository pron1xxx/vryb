<?php

namespace mycls;

class Db
{

    private $conn;
    private static $instance = null;
    private $transactionLevel = 0; // Для поддержки вложенных транзакций

    private function __construct() {}

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

     public static function get_instance() 
    {
        return self::getInstance();
    }
    
    public function getConnection(array $db_config)
    {
        if ($this->conn === null) {
            $dsn = "mysql:host={$db_config['dbhost']};dbname={$db_config['dbname']};charset={$db_config['charset']}";

            $default_options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_PERSISTENT => $db_config['persistent'] ?? false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_config['charset']}"
            ];

            $options = array_merge($default_options, $db_config['options'] ?? []);

            try {
                $this->conn = new \PDO($dsn, $db_config['username'], $db_config['password'], $options);
            } catch (\PDOException $e) {
                // Логирование ошибки
                error_log("Database connection failed: " . $e->getMessage());
                http_response_code(500);
                require VIEWS . '/errors/500.tpl.php';
                exit;
            }
        }

        return $this->conn;
    }

    public function query(string $query, array $params = [])
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " Query: " . $query);
            throw $e;
        }
    }

    public function beginTransaction(): bool
    {
        try {
            if ($this->transactionLevel === 0) {
                $result = $this->conn->beginTransaction();
            } else {
                // Для вложенных транзакций эмулируем через SAVEPOINT
                $savepointName = 'TRANSACTION_' . $this->transactionLevel;
                $this->conn->exec("SAVEPOINT $savepointName");
                $result = true;
            }

            $this->transactionLevel++;
            return $result;
        } catch (\PDOException $e) {
            error_log("Begin transaction failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function commit(): bool
    {
        if ($this->transactionLevel === 0) {
            throw new \Exception("No active transaction to commit");
        }

        $this->transactionLevel--;

        try {
            if ($this->transactionLevel === 0) {
                return $this->conn->commit();
            } else {
                // Для вложенных транзакций - ничего не делаем
                return true;
            }
        } catch (\PDOException $e) {
            error_log("Commit failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function rollBack(): bool
    {
        if ($this->transactionLevel === 0) {
            throw new \Exception("No active transaction to rollback");
        }

        try {
            if ($this->transactionLevel === 1) {
                // Основная транзакция
                $result = $this->conn->rollBack();
            } else {
                // Вложенная транзакция - откатываем до точки сохранения
                $savepointName = 'TRANSACTION_' . ($this->transactionLevel - 1);
                $this->conn->exec("ROLLBACK TO SAVEPOINT $savepointName");
                $result = true;
            }

            $this->transactionLevel--;
            return $result;
        } catch (\PDOException $e) {
            error_log("Rollback failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function inTransaction(): bool
    {
        return $this->transactionLevel > 0;
    }

    public function lastInsertId(string $name = null): string
    {
        $id = $this->conn->lastInsertId($name);
        if (!$id || $id == 0) {
            return $this->fetchColumn("SELECT LAST_INSERT_ID()");
        }
        else { 
            return $id;
        }
    }

    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }

    public function fetch(string $query, array $params = [])
    {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }

    public function fetchColumn(string $query, array $params = [], int $columnNumber = 0)
    {
        $stmt = $this->query($query, $params);
        return $stmt->fetchColumn($columnNumber);
    }

    public function execute(string $query, array $params = []): int
    {
        $stmt = $this->query($query, $params);
        return $stmt->rowCount();
    }

    public function quote($value, $parameter_type = \PDO::PARAM_STR): string
    {
        return $this->conn->quote($value, $parameter_type);
    }

    public function getPdo(): \PDO
    {
        return $this->conn;
    }

    public function disconnect(): void
    {
        $this->conn = null;
        self::$instance = null;
    }
}
