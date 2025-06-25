<?php
class Order {
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";
    
    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $payment_method;
    public $shipping_address;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create order
    public function create() {
        try {
            $this->conn->beginTransaction();
            
            // Insert order
            $query = "INSERT INTO " . $this->table_name . "
                     SET user_id=:user_id, total_amount=:total_amount, 
                         status=:status, payment_method=:payment_method, 
                         shipping_address=:shipping_address";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':total_amount', $this->total_amount);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->bindParam(':shipping_address', $this->shipping_address);
            
            if($stmt->execute()) {
                $order_id = $this->conn->lastInsertId();
                
                // Get cart items
                $query = "SELECT c.*, p.price 
                         FROM cart_items c
                         JOIN products p ON c.product_id = p.id
                         WHERE c.user_id = :user_id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $this->user_id);
                $stmt->execute();
                
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Insert order items
                foreach($cart_items as $item) {
                    $query = "INSERT INTO " . $this->items_table . "
                             SET order_id=:order_id, product_id=:product_id, 
                                 quantity=:quantity, price=:price";
                    
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $item['product_id']);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                    
                    // Update product stock
                    $query = "UPDATE products 
                             SET stock_quantity = stock_quantity - :quantity 
                             WHERE id = :product_id";
                    
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':product_id', $item['product_id']);
                    $stmt->execute();
                }
                
                // Clear cart
                $query = "DELETE FROM cart_items WHERE user_id = :user_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $this->user_id);
                $stmt->execute();
                
                $this->conn->commit();
                $this->id = $order_id;
                return true;
            }
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Get user orders
    public function getUserOrders() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get order details
    public function getOrderDetails() {
        $query = "SELECT o.*, u.name as user_name, u.email 
                 FROM " . $this->table_name . " o
                 JOIN users u ON o.user_id = u.id
                 WHERE o.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get order items
    public function getOrderItems() {
        $query = "SELECT oi.*, p.name, p.image_url, p.size 
                 FROM " . $this->items_table . " oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update order status
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                 SET status = :status 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>