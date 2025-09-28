<?php
require_once '../factories/ModelFactory.php';

class CartController {
    private $cartFactory;
    
    public function __construct() {
        $factoryManager = FactoryManager::getInstance();
        $this->cartFactory = $factoryManager->getFactory('cart');
    }
    
    // Mostra o carrinho
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login'); exit();
        }
        
        $cart = $this->cartFactory->createModel();
        $cart->user_id = $_SESSION['user_id'];
        $stmt = $cart->getCartItems();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $cart->getCartTotal();
        
        include '../views/cart/index.php';
    }
    
    // Adiciona ao carrinho
    public function add() {
        ob_clean();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Faça login primeiro']); exit();
        }
        
        // Agora precisa do ID do produto e do tamanho
        if(!isset($_POST['product_id']) || !isset($_POST['size'])) {
            echo json_encode(['success' => false, 'message' => 'Produto ou tamanho não especificado']); exit();
        }
        
        $cart = $this->cartFactory->createModel();
        $cart->user_id = $_SESSION['user_id'];
        $cart->product_id = $_POST['product_id'];
        $cart->size = $_POST['size'];
        $cart->quantity = $_POST['quantity'] ?? 1;

        if($cart->addToCart()) {
            $_SESSION['cart_count'] = $cart->getCartCount();
            echo json_encode(['success' => true, 'message' => 'Produto adicionado!', 'cart_count' => $_SESSION['cart_count']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar ao carrinho']);
        }
        exit();
    }
    
    // ... (demais funções do controller: update, remove, clear) ...
    public function update() {
        ob_clean();
        header('Content-Type: application/json');
        
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit();
        }
        
        if($_POST && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
            $cart = $this->cartFactory->createModel();
            $cart->id = $_POST['cart_id'];
            $cart->user_id = $_SESSION['user_id'];
            $cart->quantity = $_POST['quantity'];
            
            if($cart->updateQuantity()) {
                $_SESSION['cart_count'] = $cart->getCartCount();
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        }
        exit();
    }
    
    public function remove($id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $cart = $this->cartFactory->createModel();
        $cart->id = $id;
        $cart->user_id = $_SESSION['user_id'];
        
        if($cart->removeFromCart()) {
            $_SESSION['message'] = "Item removido do carrinho.";
            $_SESSION['cart_count'] = $cart->getCartCount();
        } else {
            $_SESSION['error'] = "Erro ao remover item.";
        }
        
        header('Location: index.php?action=cart');
        exit();
    }
    
    public function clear() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $cart = $this->cartFactory->createModel();
        $cart->user_id = $_SESSION['user_id'];
        
        if($cart->clearCart()) {
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