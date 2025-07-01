<?php
class Product {
    private $conn;
    private $table_name = "products";
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $size;
    public $stock_quantity;
    public $image_url;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all products
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Read single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->category = $row['category'];
            $this->size = $row['size'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image_url = $row['image_url'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }
    
    // Create product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET name=:name, description=:description, price=:price,
                    category=:category, size=:size, stock_quantity=:stock_quantity,
                    image_url=:image_url";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":size", $this->size);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":image_url", $this->image_url);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Update product
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET name = :name,
                    description = :description,
                    price = :price,
                    category = :category,
                    size = :size,
                    stock_quantity = :stock_quantity,
                    image_url = :image_url
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->size = htmlspecialchars(strip_tags($this->size));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':size', $this->size);
        $stmt->bindParam(':stock_quantity', $this->stock_quantity);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Search products
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE name LIKE ? OR description LIKE ? OR category LIKE ?
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        
        $stmt->execute();
        return $stmt;
    }
    
    // Update stock
    public function updateStock($quantity) {
        $query = "UPDATE " . $this->table_name . "
                SET stock_quantity = stock_quantity - :quantity
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    // Get all orders (admin)
    public function getAllOrders() {
        $query = "SELECT o.*, u.name as user_name, u.email
                FROM " . $this->table_name . " o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?>