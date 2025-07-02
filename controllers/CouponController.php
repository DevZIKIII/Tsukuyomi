<?php
require_once '../factories/ModelFactory.php';

class CouponController {
    private $couponFactory;
    
    public function __construct() {
        // Usar o FactoryManager para obter a factory de cupons
        $factoryManager = FactoryManager::getInstance();
        $this->couponFactory = $factoryManager->getFactory('coupon');
    }
    
    // List all coupons (admin)
    public function index() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $coupon = $this->couponFactory->createModel();
        $stmt = $coupon->read();
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
            // Usar factory para criar cupom baseado no tipo
            if($_POST['discount_type'] === 'percentage') {
                $coupon = $this->couponFactory->createPercentageCoupon(
                    $_POST['code'],
                    $_POST['description'],
                    $_POST['discount_value'],
                    $_POST['min_order_value'] ?? 0
                );
            } else {
                $coupon = $this->couponFactory->createFixedCoupon(
                    $_POST['code'],
                    $_POST['description'],
                    $_POST['discount_value'],
                    $_POST['min_order_value'] ?? 0
                );
            }
            
            // Definir propriedades adicionais
            $coupon->max_discount = $_POST['max_discount'] ?? null;
            $coupon->usage_limit = $_POST['usage_limit'] ?? null;
            $coupon->valid_from = $_POST['valid_from'];
            $coupon->valid_until = $_POST['valid_until'];
            
            if($coupon->create()) {
                $_SESSION['message'] = "Cupom criado com sucesso!";
                header('Location: index.php?action=coupons');
            } else {
                $_SESSION['error'] = "Erro ao criar cupom.";
                header('Location: index.php?action=create_coupon');
            }
        }
    }
    
    // Edit coupon form (admin)
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $coupon = $this->couponFactory->createModel();
        $coupon->id = $id;
        $coupon->readOne();
        
        include '../views/admin/coupons/edit.php';
    }
    
    // Update coupon (admin)
    public function update($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        if($_POST) {
            $coupon = $this->couponFactory->createModel();
            $coupon->id = $id;
            $coupon->code = $_POST['code'];
            $coupon->description = $_POST['description'];
            $coupon->discount_type = $_POST['discount_type'];
            $coupon->discount_value = $_POST['discount_value'];
            $coupon->min_order_value = $_POST['min_order_value'] ?? 0;
            $coupon->max_discount = $_POST['max_discount'] ?? null;
            $coupon->usage_limit = $_POST['usage_limit'] ?? null;
            $coupon->valid_from = $_POST['valid_from'];
            $coupon->valid_until = $_POST['valid_until'];
            
            if($coupon->update()) {
                $_SESSION['message'] = "Cupom atualizado com sucesso!";
                header('Location: index.php?action=coupons');
            } else {
                $_SESSION['error'] = "Erro ao atualizar cupom.";
                header('Location: index.php?action=edit_coupon&id=' . $id);
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
            $coupon = $this->couponFactory->createModel();
            $result = $coupon->validateCoupon($_POST['code'], $_POST['total']);
            
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
        
        $coupon = $this->couponFactory->createModel();
        $coupon->id = $id;
        
        if($coupon->toggleStatus()) {
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
        
        $coupon = $this->couponFactory->createModel();
        $coupon->id = $id;
        
        if($coupon->delete()) {
            $_SESSION['message'] = "Cupom excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir cupom.";
        }
        
        header('Location: index.php?action=coupons');
    }
}
?>