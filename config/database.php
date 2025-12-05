<?php
class Database {
    private $host = "localhost";
    private $port = "5432"; // port PostgreSQL (biasanya 5432 atau 5433)
    private $db   = "sistem_manajemen_kampus";
    private $user = "postgre";
    private $pass = "sabrina";

    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            // DSN PostgreSQL
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db}";

            $this->conn = new PDO($dsn, $this->user, $this->pass);

            // Error mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (Exception $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
