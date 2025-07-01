<?php
require_once '../config/database.php';
require_once '../models/Coupon.php';

class CouponController {
    private $db;
    private $coupon;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->coupon = new Coupon($this->db);
    }
    
    // List all coupons (admin)
    public function index() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $stmt = $this->coupon->read();
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/admin/coupons/index.php';
    }
    
    // Create coupon form (admin)
    public function create() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        include '../views/admin/coupons/create.php';
    }
    
    // Store coupon (admin)
    public function store() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        if($_POST) {
            $this->coupon->code = $_POST['code'];
            $this->coupon->description = $_POST['description'];
            $this->coupon->discount_type = $_POST['discount_type'];
            $this->coupon->discount_value = $_POST['discount_value'];
            $this->coupon->min_order_value = $_POST['min_order_value'] ?? 0;
            $this->coupon->max_discount = $_POST['max_discount'] ?? null;
            $this->coupon->usage_limit = $_POST['usage_limit'] ?? null;
            $this->coupon->valid_from = $_POST['valid_from'];
            $this->coupon->valid_until = $_POST['valid_until'];
            
            if($this->coupon->create()) {
                $_SESSION['message'] = "Cupom criado com sucesso!";
                header('Location: index.php?action=coupons');
            } else {
                $_SESSION['error'] = "Erro ao criar cupom.";
                header('Location: index.php?action=create_coupon');
            }
        }
    }
    
    // Validate coupon (AJAX)
    public function validate() {
        ob_clean();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['valid' => false, 'message' => 'Faça login primeiro']);
            exit();
        }
        
        if($_POST && isset($_POST['code']) && isset($_POST['total'])) {
            $result = $this->coupon->validateCoupon($_POST['code'], $_POST['total']);
            
            if($result['valid']) {
                $_SESSION['coupon'] = $result;
            }
            
            echo json_encode($result);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Dados inválidos']);
        }
        exit();
    }
    
    // Remove coupon from session
    public function remove() {
        ob_clean();
        header('Content-Type: application/json');
        
        if(isset($_SESSION['coupon'])) {
            unset($_SESSION['coupon']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }
    
    // Toggle status (admin)
    public function toggleStatus($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $this->coupon->id = $id;
        
        if($this->coupon->toggleStatus()) {
            $_SESSION['message'] = "Status do cupom atualizado!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar status.";
        }
        
        header('Location: index.php?action=coupons');
    }
    
    // Delete coupon (admin)
    public function delete($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $this->coupon->id = $id;
        
        if($this->coupon->delete()) {
            $_SESSION['message'] = "Cupom excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir cupom.";
        }
        
        header('Location: index.php?action=coupons');
    }
}
?>