<?php
// classes/Database.php - Database Wrapper Class
class Database {
    private $connection;

    public function __construct($existingConnection) {
        $this->connection = $existingConnection;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        if (empty($params)) {
            return mysqli_query($this->connection, $sql);
        }
        
        // For prepared statements, we'll use a simple approach
        $stmt = mysqli_prepare($this->connection, $sql);
        if ($stmt && !empty($params)) {
            $types = str_repeat('s', count($params)); // Assume all strings for simplicity
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            return mysqli_stmt_get_result($stmt);
        }
        return false;
    }

    public function escape($string) {
        return mysqli_real_escape_string($this->connection, $string);
    }
}
?>