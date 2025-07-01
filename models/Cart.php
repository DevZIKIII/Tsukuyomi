<?php
class Cart {
    private $conn;
    private $table_name = "cart_items";
    
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add to cart
    public function addToCart() {
        // Check if product already exists in cart
        $query = "SELECT id, quantity FROM " . $this->table_name . " 
                WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            // Update quantity
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $row['quantity'] + $this->quantity;
            
            $update_query = "UPDATE " . $this->table_name . " 
                    SET quantity = :quantity WHERE id = :id";
            
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $new_quantity);
            $update_stmt->bindParam(':id', $row['id']);
            
            return $update_stmt->execute();
        } else {
            // Insert new item
            $insert_query = "INSERT INTO " . $this->table_name . "
                    SET user_id=:user_id, product_id=:product_id, quantity=:quantity";
            
            $insert_stmt = $this->conn->prepare($insert_query);
            
            $insert_stmt->bindParam(':user_id', $this->user_id);
            $insert_stmt->bindParam(':product_id', $this->product_id);
            $insert_stmt->bindParam(':quantity', $this->quantity);
            
            return $insert_stmt->execute();
        }
    }
    
    // Get cart items
    public function getCartItems() {
        $query = "SELECT c.id, c.product_id, c.quantity, c.created_at,
                        p.name, p.price, p.image_url, p.size, p.stock_quantity
                FROM " . $this->table_name . " c
                LEFT JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id
                ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update quantity
    public function updateQuantity() {
        $query = "UPDATE " . $this->table_name . "
                SET quantity = :quantity
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    // Remove from cart
    public function removeFromCart() {
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    // Clear cart
    public function clearCart() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    // Get cart total
    public function getCartTotal() {
        $query = "SELECT SUM(c.quantity * p.price) as total
                FROM " . $this->table_name . " c
                LEFT JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }
    
    // Get cart count
    public function getCartCount() {
        $query = "SELECT SUM(quantity) as count FROM " . $this->table_name . " 
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ? $row['count'] : 0;
    }
    
    // Get cart items count (número de itens diferentes, não quantidade total)
    public function getCartItemsCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ? $row['count'] : 0;
    }
}
?>