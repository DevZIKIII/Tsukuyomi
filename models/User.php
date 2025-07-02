<?php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $user_type;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET name=:name, email=:email, password=:password,
                    phone=:phone, address=:address, city=:city,
                    state=:state, zip_code=:zip_code, user_type=:user_type";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->zip_code = htmlspecialchars(strip_tags($this->zip_code));
        $this->user_type = htmlspecialchars(strip_tags($this->user_type));
        
        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":zip_code", $this->zip_code);
        $stmt->bindParam(":user_type", $this->user_type);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Login user
    public function login() {
        $query = "SELECT id, name, email, password, user_type FROM " . $this->table_name . " 
                WHERE email = :email LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->user_type = $row['user_type'];
            return true;
        }
        
        return false;
    }
    
    // Read single user
    public function readOne() {
    $query = "SELECT 
                id, name, email, phone, address, city, state, zip_code, 
                user_type, created_at
              FROM users 
              WHERE id = ? 
              LIMIT 0,1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($row) {
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->phone = $row['phone'] ?? '';
        $this->address = $row['address'] ?? '';
        $this->city = $row['city'] ?? '';
        $this->state = $row['state'] ?? '';
        $this->zip_code = $row['zip_code'] ?? '';
        $this->user_type = $row['user_type'];
        $this->created_at = $row['created_at'] ?? '';
        
        // Só define updated_at se existir na tabela
        $this->updated_at = $row['updated_at'] ?? null;
        
        return true;
    }
    
    return false;
    }

    
    // Update user
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET name = :name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    city = :city,
                    state = :state,
                    zip_code = :zip_code
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->zip_code = htmlspecialchars(strip_tags($this->zip_code));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':zip_code', $this->zip_code);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read all users
    public function read() {
        $query = "SELECT id, name, email, phone, city, state, user_type, created_at 
                FROM " . $this->table_name . " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Check if email exists
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>