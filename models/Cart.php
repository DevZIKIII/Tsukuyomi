<?php
class Cart {
    private $conn;
    private $table_name = "cart_items";
    
    public $id;
    public $user_id;
    public $product_id;
    public $size; // Nova propriedade
    public $quantity;
    
    public function __construct($db) {
        $this->conn = $db; // CORREÇÃO: trocado "." por "->"
    }
    
    // Adiciona ao carrinho (lógica atualizada para incluir tamanho)
    public function addToCart() {
        // Verifica se o item (mesmo produto e mesmo tamanho) já existe
        $query = "SELECT id, quantity FROM " . $this->table_name . " WHERE user_id = :user_id AND product_id = :product_id AND size = :size";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':size', $this->size);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            // Atualiza a quantidade se já existir
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $row['quantity'] + $this->quantity;
            
            $update_query = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query); // CORREÇÃO: trocado "." por "->"
            $update_stmt->bindParam(':quantity', $new_quantity);
            $update_stmt->bindParam(':id', $row['id']);
            return $update_stmt->execute();
        } else {
            // Insere novo item
            $insert_query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, product_id=:product_id, size=:size, quantity=:quantity";
            $insert_stmt = $this->conn->prepare($insert_query); // CORREÇÃO: trocado "." por "->"
            $insert_stmt->bindParam(':user_id', $this->user_id);
            $insert_stmt->bindParam(':product_id', $this->product_id);
            $insert_stmt->bindParam(':size', $this->size);
            $insert_stmt->bindParam(':quantity', $this->quantity);
            return $insert_stmt->execute();
        }
    }
    
    // Pega os itens do carrinho (query atualizada para incluir tamanho e estoque)
    public function getCartItems() {
        $query = "SELECT 
                    c.id, 
                    c.product_id, 
                    c.quantity, 
                    c.size, 
                    p.name, 
                    p.price, 
                    p.image_url,
                    pv.stock_quantity
                  FROM " . $this->table_name . " c
                  LEFT JOIN products p ON c.product_id = p.id
                  LEFT JOIN product_variants pv ON c.product_id = pv.product_id AND c.size = pv.size
                  WHERE c.user_id = :user_id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        return $stmt;
    }

    // ... (demais funções: getCartTotal, updateQuantity, etc. não precisam de grandes mudanças) ...
    public function updateQuantity() {
        $query = "UPDATE " . $this->table_name . "
                SET quantity = :quantity
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    public function removeFromCart() {
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    public function clearCart() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }
    
    public function getCartTotal() {
        $query = "SELECT SUM(c.quantity * p.price) as total
                FROM " . $this->table_name . " c
                LEFT JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ? $row['total'] : 0;
    }
    
    public function getCartCount() {
        $query = "SELECT SUM(quantity) as count FROM " . $this->table_name . " 
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query); // CORREÇÃO: trocado "." por "->"
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ? $row['count'] : 0;
    }
}
?>