<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Database
{

    // specify your own database credentials
    private $host = '127.0.0.1';
    private $db = 'blog';
    private $port = '3306';
    private $user = 'root';
    private $pass = 'root';
    private $charset = 'utf8mb4';
    public $conn;

    private $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
    // get the database connection
    public function getConnection()
    {
        $this->conn = null;
        $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db . ";charset=" . $this->charset;

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $this->options);
        } catch (PDOException $exception) {
            http_response_code(500);
            echo json_encode("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
