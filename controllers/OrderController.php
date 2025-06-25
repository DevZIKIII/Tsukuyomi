<?php
//session_start();
require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../models/Cart.php';

class OrderController {
    private $db;
    private $order;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->order = new Order($this->db);
    }
    
    // Create order
    public function create() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        if($_POST) {
            // Get cart total
            $cart = new Cart($this->db);
            $cart->user_id = $_SESSION['user_id'];
            $total = $cart->getCartTotal();
            
            if($total > 0) {
                $this->order->user_id = $_SESSION['user_id'];
                $this->order->total_amount = $total;
                $this->order->status = 'pending';
                $this->order->payment_method = $_POST['payment_method'];
                $this->order->shipping_address = $_POST['shipping_address'];
                
                if($this->order->create()) {
                    $_SESSION['message'] = "Pedido realizado com sucesso! Número do pedido: " . $this->order->id;
                    header('Location: /tsukuyomi/public/index.php?action=order_success&id=' . $this->order->id);
                } else {
                    $_SESSION['error'] = "Erro ao processar pedido.";
                    header('Location: /tsukuyomi/public/index.php?action=cart');
                }
            } else {
                $_SESSION['error'] = "Carrinho vazio.";
                header('Location: /tsukuyomi/public/index.php?action=cart');
            }
        }
    }
    
    // Show user orders
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        $this->order->user_id = $_SESSION['user_id'];
        $stmt = $this->order->getUserOrders();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/orders/index.php';
    }
    
    // Show order details
    public function show($id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        $this->order->id = $id;
        $order = $this->order->getOrderDetails();
        
        // Check if order belongs to user or user is admin
        if($order['user_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] != 'admin') {
            header('Location: /tsukuyomi/public/index.php');
            exit();
        }
        
        $stmt = $this->order->getOrderItems();
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/orders/show.php';
    }
    
    // Update order status (admin only)
    public function updateStatus() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: /tsukuyomi/public/index.php');
            exit();
        }
        
        if($_POST) {
            $this->order->id = $_POST['order_id'];
            $this->order->status = $_POST['status'];
            
            if($this->order->updateStatus()) {
                $_SESSION['message'] = "Status do pedido atualizado.";
            } else {
                $_SESSION['error'] = "Erro ao atualizar status.";
            }
            
            header('Location: /tsukuyomi/public/index.php?action=order&id=' . $this->order->id);
        }
    }
}
?>