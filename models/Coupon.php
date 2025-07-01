<?php
class Coupon {
    private $conn;
    private $table_name = "coupons";
    
    public $id;
    public $code;
    public $description;
    public $discount_type;
    public $discount_value;
    public $min_order_value;
    public $max_discount;
    public $usage_limit;
    public $used_count;
    public $valid_from;
    public $valid_until;
    public $is_active;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create coupon
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET code=:code, description=:description, discount_type=:discount_type,
                    discount_value=:discount_value, min_order_value=:min_order_value,
                    max_discount=:max_discount, usage_limit=:usage_limit,
                    valid_from=:valid_from, valid_until=:valid_until";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->code = strtoupper(htmlspecialchars(strip_tags($this->code)));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->discount_type = htmlspecialchars(strip_tags($this->discount_type));
        $this->discount_value = htmlspecialchars(strip_tags($this->discount_value));
        $this->min_order_value = htmlspecialchars(strip_tags($this->min_order_value));
        $this->max_discount = $this->max_discount ? htmlspecialchars(strip_tags($this->max_discount)) : null;
        $this->usage_limit = $this->usage_limit ? htmlspecialchars(strip_tags($this->usage_limit)) : null;
        $this->valid_from = htmlspecialchars(strip_tags($this->valid_from));
        $this->valid_until = htmlspecialchars(strip_tags($this->valid_until));
        
        // Bind values
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":discount_type", $this->discount_type);
        $stmt->bindParam(":discount_value", $this->discount_value);
        $stmt->bindParam(":min_order_value", $this->min_order_value);
        $stmt->bindParam(":max_discount", $this->max_discount);
        $stmt->bindParam(":usage_limit", $this->usage_limit);
        $stmt->bindParam(":valid_from", $this->valid_from);
        $stmt->bindParam(":valid_until", $this->valid_until);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read all coupons
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Validate coupon
    public function validateCoupon($code, $order_total) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE code = :code 
                AND is_active = TRUE 
                AND valid_from <= CURDATE() 
                AND valid_until >= CURDATE()
                AND (usage_limit IS NULL OR used_count < usage_limit)
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $code = strtoupper($code);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check minimum order value
            if($order_total < $row['min_order_value']) {
                return [
                    'valid' => false,
                    'message' => 'Pedido mínimo de R$ ' . number_format($row['min_order_value'], 2, ',', '.') . ' para usar este cupom'
                ];
            }
            
            // Calculate discount
            $discount = 0;
            if($row['discount_type'] == 'percentage') {
                $discount = $order_total * ($row['discount_value'] / 100);
                if($row['max_discount'] && $discount > $row['max_discount']) {
                    $discount = $row['max_discount'];
                }
            } else {
                $discount = $row['discount_value'];
            }
            
            return [
                'valid' => true,
                'coupon_id' => $row['id'],
                'code' => $row['code'],
                'description' => $row['description'],
                'discount_type' => $row['discount_type'],
                'discount_value' => $row['discount_value'],
                'discount_amount' => $discount,
                'message' => 'Cupom aplicado com sucesso!'
            ];
        }
        
        return [
            'valid' => false,
            'message' => 'Cupom inválido ou expirado'
        ];
    }
    
    // Update usage count
    public function incrementUsage($coupon_id) {
        $query = "UPDATE " . $this->table_name . " 
                SET used_count = used_count + 1 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $coupon_id);
        
        return $stmt->execute();
    }
    
    // Toggle active status
    public function toggleStatus() {
        $query = "UPDATE " . $this->table_name . " 
                SET is_active = !is_active 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    // Delete coupon
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>