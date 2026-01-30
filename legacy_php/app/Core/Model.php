<?php
namespace App\Core;

use PDO;
use PDOException;
use Database;

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct()
    {
        $this->db = $this->getConnection();
    }
    
    private function getConnection()
    {
        $configFile = __DIR__ . '/../config/database.php';
        
        if (!file_exists($configFile)) {
            die("Database config file not found: " . $configFile);
        }
        
        require_once $configFile;
        
        // Đảm bảo Database class tồn tại
        if (!class_exists('Database')) {
            die("Database class not found. Check your database.php file");
        }
        
        return Database::getInstance()->getConnection();
    }
    
    public function selectAll($columns = ['*'])
    {
        $columns = implode(', ', $columns); // ✅ THÊM KHOẢNG TRẮNG
        $stmt = $this->db->prepare("SELECT {$columns} FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ fetch() thay vì fetchAll()
    }
    
    public function create(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
        return $stmt->execute($data);
    }
    
    public function update($id, array $data)
    {
        // ✅ SỬA LỖI NGHIÊM TRỌNG: :{$key} thay vì :{$value}
        $setData = '';
        foreach ($data as $key => $value) {
            $setData .= "{$key} = :{$key}, "; // ✅ ĐÚNG
        }
        $setData = rtrim($setData, ', ');
        
        // Thêm id vào data để bind
        $data[$this->primaryKey] = $id;
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setData} WHERE {$this->primaryKey} = :{$this->primaryKey}");
        return $stmt->execute($data);
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function softDelete($id)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_deleted = 1 WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    public function commit()
    {
        return $this->db->commit();
    }
    
    public function rollback()
    {
        return $this->db->rollBack();
    }

    protected function generateUuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        
        // Fallback
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}