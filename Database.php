<?php
namespace MuhthishimisCoding\PreFramework;

class Database
{
    public const EQUAL = '=';
    public const UNEQUAL = '!=';
    public const LESS = '<';
    public const GREATER = '>';
    public const LESSANDEQUAL = '<=';
    public const GREATANDEQUAL = '>=';
    protected $errors;
    protected $db;
    public \PDO $pdo;
    public function __construct(array $config, $errorReporting)
    {
        try {
            $this->db = $config['db'];
            $this->pdo = new \PDO($config['dsn'], $config['user'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->errors = $errorReporting;
        } catch (\PDOException $e) {
            Application::app()->error->handleException($e);
        }
    }
    public function tableExists(string $table): bool
    {
        return $this->pdo->exec("SHOW TABLES LIKE '$table'") !== 0;
    }
    public function applyMigrations()
    {
        $this->createMigrationTable();
        /**
         * When all migrations would be applied then we would add this
         * array into migrations table to track applied migrations*/
        $newMigrations = [];

        $files = scandir(Application::$ROOT_DIR . '/migrations');
        // All migrations to apply
        $migToApply = array_diff($files, $this->getAppliedMigrations());
        // for removing . and .. for folders
        unset($migToApply[0], $migToApply[1]);
        foreach ($migToApply as $migration) {
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instanse = new $className();
            $this->log('Applying migration.');
            $instanse->up();
            $this->log('Successfully Applied migrations');
            $newMigrations[] = "('$migration')";
        }
        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log('All migrations are already applied');
        }
    }

    public function insert(string $table, array $col_values, int $escapehtml = 0)
    {
        $columnsArray = array_keys($col_values);
        $columns = implode(', ', $columnsArray);
        $values = implode(', :', $columnsArray);
        $sql = "INSERT INTO $table($columns) VALUES(:$values)";
        try {
            if ($this->handleStmtbind($sql, $col_values, null, $escapehtml)) {
                // $this->result[] = $this->pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            Application::app()->error->handleException($e, 'UnKnown error occured please try again letter.');
            return false;

        }


    }
    protected function stringWhere(array &$where, ?int $useAndOperator, $comOperator): string
    {
        $stw = '';
        if ($useAndOperator) {
            foreach ($where as $key => $value) {
                $stw .= "$key $comOperator:$key AND ";
            }
            $stw = substr($stw, 0, -5);
        } else {
            foreach ($where as $key => $value) {
                $stw .= "$key $comOperator:$key OR ";
            }
            $stw = substr($stw, 0, -4);
        }
        return $stw;
    }
    public function delete(
        string $table,
        string|null|array $where = null,
        string $comOperator = self::EQUAL,
        int|null $useAndOperator = 1,
        $escapehtml = 0
    ) {
        // if ($this->tableExists($table)) {
        $sql = "DELETE FROM $table";
        if ($where !== null) {
            if (is_array($where)) {
                $stw = $this->stringWhere($where, $useAndOperator, $comOperator);
                $sql .= " WHERE $stw ";
                if ($this->handleStmtbind($sql, $where, null, $escapehtml)) {
                    // $this->result[] = $this->mysqli->affected_rows;
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($this->runQuery($sql, $where)) {
                    // $this->result[] = $this->mysqli->affected_rows;
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if ($this->runQuery($sql)) {
                // $this->result[] = $this->mysqli->affected_rows;
                return true;
            } else {
                return false;
            }
        }
        // }
    }
    public function update(
        string $table,
        array $col_values,
        string|array|null $where = null,
        int|null $useAndOperator = 1,
        string $comOperator = '=',
        $escapehtml = 0
    ) {
        // if ($this->tableExists($table)) {
        $columns = '';
        foreach ($col_values as $key => $value) {
            $columns .= "$key =:$key,";
        }
        $columns = substr($columns, 0, -1);
        $sql = "UPDATE $table SET $columns";
        if ($where != null) {
            if (is_array($where)) {
                $stw = $this->stringWhere($where, $useAndOperator, $comOperator);
                $sql .= " WHERE $stw ";
                $col_values = array_merge($col_values, $where);
            } else {
                $sql .= " WHERE $where";
            }
        }
        if ($this->handleStmtbind($sql, $col_values, null, $escapehtml)) {
            // $this->result[] = $this->pdo->
            return true;
        } else {
            return false;
        }
        // } else {
        //     return false;
        // }
    }
    protected function OLF(&$sql, $orderby, $limit, $offset)
    {
        if ($orderby !== null) {
            $sql .= " ORDER BY $orderby";
        }
        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }
    }
    public function getcolumns(string $table, int $ras = 0, string|null $database = null)
    {
        if ($database === null) {
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$this->db' AND TABLE_NAME = '$table'";
        } else {
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'";
        }
        $result = $this->runquery($sql, null, 1);
        if ($ras === 0) {
            return $result;
        } else {
            $data = $result->fetchAll(\PDO::FETCH_COLUMN);
            $columns = $data;
            // $string = implode(', ', $columns);
            if ($ras === 1) {
                return $columns;
            } else {
                return implode(', ', $columns);
            }
        }
    }
    public function select(
        string $table,
        array|string|null $where = null,
        ?string $comOperator = self::EQUAL,
        ?int $useAndOperator = 1,
        ?string $orderby = null,
        ?string $limit = null,
        ?string $offset = null,
        ?string $columns = null,
        ?int $distinct = null,
        $escapehtml = 0,

    ): array|bool|null|\PDOStatement {
        // if ($this->tableExists($table)) {
        $sql = "SELECT";

        if ($distinct != null) {
            $sql .= " DISTINCT ";
        }
        if ($columns === null) {
            $sql .= " * ";
        } else {
            $sql .= " $columns ";
        }
        $sql .= " FROM $table";

        if ($where !== null) {
            if (is_array($where)) {
                $stw = $this->stringWhere($where, $useAndOperator, $comOperator);
                $sql .= " WHERE $stw ";
                $this->OLF($sql, $orderby, $limit, $offset);
                return $this->handleStmtbind($sql, $where, 1, $escapehtml);

            } else {
                return $this->runQuery($sql, $where, 1, $orderby, $limit, $offset);

            }
        } else {
            return $this->runQuery($sql, null, 1, $orderby, $limit, $offset);
        }
        // } else {
        //     return false;
        // }
    }
    protected function handleStmtbind(string $sql, array $col_values, int|null $select = null, $escapehtml = 0)
    {
        try {
            // For showing querys and errors
            $this->dumperrors($sql);
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                $params = [];
                if ($escapehtml === 1) {
                    foreach ($col_values as $key => $value) {
                        $params[':' . $key] = htmlentities($value);
                    }
                } else {
                    foreach ($col_values as $key => $value) {
                        $params[':' . $key] = $value;
                    }
                }
                $this->dumperrors($params);
                if ($stmt->execute($params)) {
                    if ($select === null) {
                        return true;
                    } else {
                        return $stmt;
                    }
                } else {
                    return false;
                }


            }
        } catch (\PDOException $e) {
            Application::app()->error->handleException($e);
            die();
        }
    }
    public function runQuery(string $sql, string|null $where = null, int|null $select = null, string|null $orderby = null, string|null $limit = null, string|null $offset = null)
    {
        if ($where !== null) {
            $sql .= " WHERE $where";
        }
        $this->OLF($sql, $orderby, $limit, $offset);
        $this->dumperrors($sql);
        if ($select === null) {
            $stmt = $this->pdo->exec($sql);
            return $stmt;
        } else {
            $stmt = $this->pdo->query($sql);
            return $stmt;
        }
    }
    protected function dumperrors(...$args)
    {
        if ($this->errors) {
            foreach ($args as $arg) {
                print_r($arg);
            }
        }
    }
    public function saveMigrations(array $migrations)
    {

        $str = implode(',', $migrations);
        // Executing
        $this->pdo->prepare("INSERT INTO migrations(migration) VALUES $str")->execute();
    }
    public function createMigrationTable()
    {
        $this->pdo->exec('
        CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;');
    }
    public function getAppliedMigrations()
    {
        $stmt = $this->pdo->prepare('SELECT migration FROM migrations');
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function log($message)
    {
        echo '[' . date('Y-m-d h:i:s a') . '] ' . $message . PHP_EOL;
    }
}


// class Database{
//     public function __construct(
//         private string $host,
//         private string $userName,
//         private string $password,
//         private string $databse
//     ){}
//     public function createConnection():\PDO{
//         $dsn = "mysql:host={$this->host};dbname:{$this->databse};charset=utf8";

//         return new \PDO($dsn,$this->userName,$this->password,[
//             \PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION
//             ]);
//     }

// }

/**
 * All php exceptions classes
 * BadFunctionCallException
 * BadMethodCallException
 * InvalidFileException
 * InvalidPathException
 * InvalidArgumentException
 * Exception
 * RuntimeException
 * LengthException
 * LogicException
 * OutOfRangeException
 * OutOfBoundsException
 * UnderFlowException
 * UnexpectedValueException
 * 
 * Other kinde of error handlers
 * DivisionByZeroError
 */