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
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $this->conn->beginTransaction();
        try {
            // Insere o pedido principal
            $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, total_amount=:total_amount, status=:status, payment_method=:payment_method, shipping_address=:shipping_address";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':total_amount', $this->total_amount);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':payment_method', $this->payment_method);
            $stmt->bindParam(':shipping_address', $this->shipping_address);
            $stmt->execute();
            $this->id = $this->conn->lastInsertId();
            
            // Pega os itens do carrinho (agora com tamanho)
            $cart_query = "SELECT c.product_id, c.size, c.quantity, p.price FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id";
            $cart_stmt = $this->conn->prepare($cart_query);
            $cart_stmt->bindParam(':user_id', $this->user_id);
            $cart_stmt->execute();
            
            // Insere os itens do pedido (agora com tamanho)
            $item_query = "INSERT INTO " . $this->order_items_table . " SET order_id=:order_id, product_id=:product_id, size=:size, quantity=:quantity, price=:price";
            $item_stmt = $this->conn->prepare($item_query);

            $variant_stock_query = "UPDATE product_variants SET stock_quantity = stock_quantity - :quantity WHERE product_id = :product_id AND size = :size";
            $variant_stock_stmt = $this->conn->prepare($variant_stock_query);

            while($row = $cart_stmt->fetch(PDO::FETCH_ASSOC)) {
                $item_stmt->bindParam(':order_id', $this->id);
                $item_stmt->bindParam(':product_id', $row['product_id']);
                $item_stmt->bindParam(':size', $row['size']); // Salva o tamanho
                $item_stmt->bindParam(':quantity', $row['quantity']);
                $item_stmt->bindParam(':price', $row['price']);
                $item_stmt->execute();
                
                // Atualiza o estoque na tabela de variantes
                $variant_stock_stmt->bindParam(':quantity', $row['quantity']);
                $variant_stock_stmt->bindParam(':product_id', $row['product_id']);
                $variant_stock_stmt->bindParam(':size', $row['size']);
                $variant_stock_stmt->execute();
            }
            
            // Limpa o carrinho
            $clear_query = "DELETE FROM cart_items WHERE user_id = :user_id";
            $clear_stmt = $this->conn->prepare($clear_query);
            $clear_stmt->bindParam(':user_id', $this->user_id);
            $clear_stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getUserOrders() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE user_id = :user_id
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }
    
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
    
    public function getOrderItems() {
        $query = "SELECT oi.size, oi.quantity, oi.price, p.name, p.image_url
                FROM " . $this->order_items_table . " oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * FUNÇÃO ADICIONADA DE VOLTA
     * Método para listar todos os pedidos (admin)
     */
    public function getAllOrders() {
        $query = "SELECT 
                    o.id,
                    o.user_id,
                    o.total_amount,
                    o.status,
                    o.payment_method,
                    o.shipping_address,
                    o.created_at,
                    u.name as user_name,
                    u.email as user_email
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
}
?>