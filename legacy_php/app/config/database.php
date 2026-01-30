<?php 
    require_once 'env.php';
    class Database {
        private static $instance = null;
        private $conn;
        // CHỈ KHAI BÁO, KHÔNG GÁN GIÁ TRỊ TỪ HÀM
        private $host;
        private $dbname;
        private $username;
        private $password;
        private $charset;

        private function __construct() {
            // GÁN GIÁ TRỊ TRONG CONSTRUCTOR
            $this->host = Env::get('DB_HOST', 'localhost');
            $this->dbname = Env::get('DB_NAME', 'StudyHub');
            $this->username = Env::get('DB_USER', 'root');
            $this->password = Env::get('DB_PASS', '');
            $this->charset = Env::get('DB_CHARSET', 'utf8mb4');
            
            $this->connect();
        }

        public function connect() {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch(PDOException $e) {
                die("Kết nối thất bại: " . $e->getMessage());
            }
        }

        public static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new Database();
            }
            return self::$instance;
        }

        // THÊM PHƯƠNG THỨC NÀY ĐỂ LẤY KẾT NỐI
        public function getConnection() {
            return $this->conn;
        }
    }

?>