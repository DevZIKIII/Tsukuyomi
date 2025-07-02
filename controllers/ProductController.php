<?php
require_once '../factories/ModelFactory.php';

class ProductController {
    private $productFactory;
    
    public function __construct() {
        // Usar o FactoryManager para obter a factory de produtos
        $factoryManager = FactoryManager::getInstance();
        $this->productFactory = $factoryManager->getFactory('product');
    }
    
    // Display all products
    public function index() {
        $product = $this->productFactory->createModel();
        $stmt = $product->read();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/products/index.php';
    }
    
    // Show single product
    public function show($id) {
        $product = $this->productFactory->createModel();
        $product->id = $id;
        $product->readOne();
        
        include '../views/products/show.php';
    }
    
    // Create product form
    public function create() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        include '../views/products/create.php';
    }
    
    // Store product
    public function store() {
        if($_POST) {
            // Usar a factory para criar produto com dados
            $product = $this->productFactory->createProductWithData($_POST);
            
            if($product->create()) {
                $_SESSION['message'] = "Produto criado com sucesso!";
                header('Location: index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao criar produto.";
                header('Location: index.php?action=create_product');
            }
        }
    }
    
    // Edit product form
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $product = $this->productFactory->createModel();
        $product->id = $id;
        $product->readOne();
        
        include '../views/products/edit.php';
    }
    
    // Update product
    public function update($id) {
        if($_POST) {
            $product = $this->productFactory->createProductWithData($_POST);
            $product->id = $id;
            
            if($product->update()) {
                $_SESSION['message'] = "Produto atualizado com sucesso!";
                header('Location: index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao atualizar produto.";
                header('Location: index.php?action=edit_product&id=' . $id);
            }
        }
    }
    
    // Delete product
    public function destroy($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        $product = $this->productFactory->createModel();
        $product->id = $id;
        
        if($product->delete()) {
            $_SESSION['message'] = "Produto excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir produto.";
        }
        
        header('Location: index.php?action=products');
    }
    
    // Search products
    public function search() {
        $keywords = isset($_GET['q']) ? $_GET['q'] : '';
        $product = $this->productFactory->createModel();
        $stmt = $product->search($keywords);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/products/index.php';
    }
}
?>