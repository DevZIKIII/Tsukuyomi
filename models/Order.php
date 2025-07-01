<?php
class Order {
    private $conn;
    private $table_name = "orders";
    private $order_items_table = "order_items";
    
    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $payment_method;
    public $shipping_address;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create order
    public function create() {
        // Start transaction
        $this->conn->beginTransaction();
        
        try {
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
            
            if(!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }
            
            $this->id = $this->conn->lastInsertId();
            
            // Get cart items
            $cart_query = "SELECT c.product_id, c.quantity, p.price
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = :user_id";
            
            $cart_stmt = $this->conn->prepare($cart_query);
            $cart_stmt->bindParam(':user_id', $this->user_id);
            $cart_stmt->execute();
            
            // Insert order items
            while($row = $cart_stmt->fetch(PDO::FETCH_ASSOC)) {
                $item_query = "INSERT INTO " . $this->order_items_table . "
                            SET order_id=:order_id, product_id=:product_id,
                                quantity=:quantity, price=:price";
                
                $item_stmt = $this->conn->prepare($item_query);
                $item_stmt->bindParam(':order_id', $this->id);
                $item_stmt->bindParam(':product_id', $row['product_id']);
                $item_stmt->bindParam(':quantity', $row['quantity']);
                $item_stmt->bindParam(':price', $row['price']);
                
                if(!$item_stmt->execute()) {
                    throw new Exception("Failed to create order item");
                }
                
                // Update product stock
                $stock_query = "UPDATE products 
                            SET stock_quantity = stock_quantity - :quantity
                            WHERE id = :product_id";
                
                $stock_stmt = $this->conn->prepare($stock_query);
                $stock_stmt->bindParam(':quantity', $row['quantity']);
                $stock_stmt->bindParam(':product_id', $row['product_id']);
                
                if(!$stock_stmt->execute()) {
                    throw new Exception("Failed to update stock");
                }
            }
            
            // Clear cart
            $clear_query = "DELETE FROM cart WHERE user_id = :user_id";
            $clear_stmt = $this->conn->prepare($clear_query);
            $clear_stmt->bindParam(':user_id', $this->user_id);
            
            if(!$clear_stmt->execute()) {
                throw new Exception("Failed to clear cart");
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch(Exception $e) {
            // Rollback transaction
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
        $query = "SELECT o.*, u.name as user_name, u.email as user_email
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
                FROM " . $this->order_items_table . " oi
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
        
        return $stmt->execute();
    }
    
    // Get all orders (admin)
    public function getAllOrders() {
        $query = "SELECT o.*, u.name as user_name
                FROM " . $this->table_name . " o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?>