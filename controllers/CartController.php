<?php
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
            header('Location: index.php?action=login');
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
        // Limpar qualquer output anterior
        ob_clean();
        
        // Definir header para JSON
        header('Content-Type: application/json');
        
        // Array de resposta
        $response = [];
        
        try {
            // Verificar se usuário está logado
            if(!isset($_SESSION['user_id'])) {
                $response = ['success' => false, 'message' => 'Faça login primeiro'];
                echo json_encode($response);
                exit();
            }
            
            // Verificar se recebeu dados POST
            if(!isset($_POST['product_id'])) {
                $response = ['success' => false, 'message' => 'ID do produto não fornecido'];
                echo json_encode($response);
                exit();
            }
            
            // Configurar dados do carrinho
            $this->cart->user_id = $_SESSION['user_id'];
            $this->cart->product_id = $_POST['product_id'];
            $this->cart->quantity = $_POST['quantity'] ?? 1;
            
            // Tentar adicionar ao carrinho
            if($this->cart->addToCart()) {
                // Atualizar contagem do carrinho na sessão
                $_SESSION['cart_count'] = $this->cart->getCartCount();
                $response = [
                    'success' => true, 
                    'message' => 'Produto adicionado ao carrinho',
                    'cart_count' => $_SESSION['cart_count']
                ];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao adicionar ao carrinho'];
            }
            
        } catch (Exception $e) {
            $response = [
                'success' => false, 
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
        
        // Enviar resposta JSON
        echo json_encode($response);
        exit();
    }
    
    // Update quantity
    public function update() {
        ob_clean();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit();
        }
        
        if($_POST && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
            $this->cart->id = $_POST['cart_id'];
            $this->cart->user_id = $_SESSION['user_id'];
            $this->cart->quantity = $_POST['quantity'];
            
            if($this->cart->updateQuantity()) {
                $_SESSION['cart_count'] = $this->cart->getCartCount();
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        }
        exit();
    }
    
    // Remove from cart
    public function remove($id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cart->id = $id;
        $this->cart->user_id = $_SESSION['user_id'];
        
        if($this->cart->removeFromCart()) {
            $_SESSION['message'] = "Item removido do carrinho.";
            $_SESSION['cart_count'] = $this->cart->getCartCount();
        } else {
            $_SESSION['error'] = "Erro ao remover item.";
        }
        
        header('Location: index.php?action=cart');
        exit();
    }
    
    // Clear cart
    public function clear() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cart->user_id = $_SESSION['user_id'];
        
        if($this->cart->clearCart()) {
            $_SESSION['message'] = "Carrinho limpo.";
            $_SESSION['cart_count'] = 0;
        } else {
            $_SESSION['error'] = "Erro ao limpar carrinho.";
        }
        
        header('Location: index.php?action=cart');
        exit();
    }
}
?>