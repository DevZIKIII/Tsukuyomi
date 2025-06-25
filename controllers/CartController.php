<?php
//session_start();
require_once '../config/database.php';
require_once '../models/Cart.php';
require_once '../models/Product.php';

class CartController {
    private $db;
    private $cart;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cart = new Cart($this->db);
    }
    
    // Show cart
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        $this->cart->user_id = $_SESSION['user_id'];
        $stmt = $this->cart->getCartItems();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->cart->getCartTotal();
        
        include '../views/cart/index.php';
    }
    
    // Add to cart
    public function add() {
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Faça login primeiro']);
            exit();
        }
        
        if($_POST) {
            $this->cart->user_id = $_SESSION['user_id'];
            $this->cart->product_id = $_POST['product_id'];
            $this->cart->quantity = $_POST['quantity'] ?? 1;
            
            if($this->cart->addToCart()) {
                // Atualizar contagem do carrinho na sessão
                $_SESSION['cart_count'] = $this->cart->getCartCount();
                echo json_encode(['success' => true, 'message' => 'Produto adicionado ao carrinho']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao adicionar ao carrinho']);
            }
        }
    }
    
    // Update quantity
    public function update() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        if($_POST) {
            $this->cart->id = $_POST['cart_id'];
            $this->cart->user_id = $_SESSION['user_id'];
            $this->cart->quantity = $_POST['quantity'];
            
            if($this->cart->updateQuantity()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }
    
    // Remove from cart
    public function remove($id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        $this->cart->id = $id;
        $this->cart->user_id = $_SESSION['user_id'];
        
        if($this->cart->removeFromCart()) {
            $_SESSION['message'] = "Item removido do carrinho.";
        } else {
            $_SESSION['error'] = "Erro ao remover item.";
        }
        
        header('Location: /tsukuyomi/public/index.php?action=cart');
    }
    
    // Clear cart
    public function clear() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /tsukuyomi/public/index.php?action=login');
            exit();
        }
        
        $this->cart->user_id = $_SESSION['user_id'];
        
        if($this->cart->clearCart()) {
            $_SESSION['message'] = "Carrinho limpo.";
        } else {
            $_SESSION['error'] = "Erro ao limpar carrinho.";
        }
        
        header('Location: /tsukuyomi/public/index.php?action=cart');
    }


}
?>