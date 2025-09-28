<?php
require_once '../factories/ModelFactory.php';

class OrderController {
    private $orderFactory;
    private $cartFactory;
    private $couponFactory;
    
    public function __construct() {
        // Usar o FactoryManager para obter as factories necessárias
        $factoryManager = FactoryManager::getInstance();
        $this->orderFactory = $factoryManager->getFactory('order');
        $this->cartFactory = $factoryManager->getFactory('cart');
        $this->couponFactory = $factoryManager->getFactory('coupon');
    }
    
    // Create order
    public function create() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        if($_POST) {
            // Get cart total
            $cart = $this->cartFactory->createModel();
            $cart->user_id = $_SESSION['user_id'];
            $total = $cart->getCartTotal();
            
            if($total > 0) {
                // Aplicar desconto do cupom se houver
                $discount = 0;
                $coupon_id = null;
                
                if(isset($_SESSION['coupon']) && $_SESSION['coupon']['valid']) {
                    $discount = $_SESSION['coupon']['discount_amount'];
                    $coupon_id = $_SESSION['coupon']['coupon_id'];
                    $total = $total - $discount;
                }
                
                // Usar factory para criar pedido (agora com status 'processing')
                $order = $this->orderFactory->createOrder( // <-- MUDANÇA AQUI
                    $_SESSION['user_id'],
                    $total,
                    $_POST['payment_method'],
                    $_POST['shipping_address']
                );
                
                if($order->create()) {
                    // Se usou cupom, incrementar o contador de uso
                    if($coupon_id) {
                        $coupon = $this->couponFactory->createModel();
                        $coupon->incrementUsage($coupon_id);
                        unset($_SESSION['coupon']);
                    }
                    
                    // Limpar contagem do carrinho
                    $_SESSION['cart_count'] = 0;
                    
                    $_SESSION['message'] = "Pedido realizado com sucesso! Número do pedido: " . $order->id;
                    header('Location: index.php?action=order&id=' . $order->id);
                    exit();
                } else {
                    $_SESSION['error'] = "Erro ao processar pedido.";
                    header('Location: index.php?action=cart');
                    exit();
                }
            } else {
                $_SESSION['error'] = "Carrinho vazio.";
                header('Location: index.php?action=cart');
                exit();
            }
        }
    }
    
    // Show user orders
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $order = $this->orderFactory->createModel();
        $order->user_id = $_SESSION['user_id'];
        $stmt = $order->getUserOrders();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/orders/index.php';
    }
    
    // Show order details
    public function show($id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $order = $this->orderFactory->createModel();
        $order->id = $id;
        $orderDetails = $order->getOrderDetails();
        
        // Check if order belongs to user or user is admin
        if(!$orderDetails || ($orderDetails['user_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] != 'admin')) {
            header('Location: index.php');
            exit();
        }
        
        $stmt = $order->getOrderItems();
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/orders/show.php';
    }
    
    // Update order status (admin only)
    public function updateStatus() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        if($_POST) {
            $order = $this->orderFactory->createModel();
            $order->id = $_POST['order_id'];
            $order->status = $_POST['status'];
            
            if($order->updateStatus()) {
                $_SESSION['message'] = "Status do pedido atualizado.";
            } else {
                $_SESSION['error'] = "Erro ao atualizar status.";
            }
            
            header('Location: index.php?action=order&id=' . $order->id);
            exit();
        }
    }
    
    // List all orders (admin)
    public function allOrders() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $order = $this->orderFactory->createModel();
        $stmt = $order->getAllOrders();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/admin/orders/index.php';
    }
}
?>