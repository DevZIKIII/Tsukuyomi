<?php
//session_start();
require_once '../config/database.php';
require_once '../models/Product.php';

class ProductController {
    private $db;
    private $product;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }
    
    // Display all products
    public function index() {
        $stmt = $this->product->read();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/products/index.php';
    }
    
    // Show single product
    public function show($id) {
        $this->product->id = $id;
        $this->product->readOne();
        
        include '../views/products/show.php';
    }
    
    // Create product form
    public function create() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: /tsukuyomi/public/index.php');
            exit();
        }
        
        include '../views/products/create.php';
    }
    
    // Store product
    public function store() {
        if($_POST) {
            $this->product->name = $_POST['name'];
            $this->product->description = $_POST['description'];
            $this->product->price = $_POST['price'];
            $this->product->category = $_POST['category'];
            $this->product->size = $_POST['size'];
            $this->product->stock_quantity = $_POST['stock_quantity'];
            $this->product->image_url = $_POST['image_url'];
            
            if($this->product->create()) {
                $_SESSION['message'] = "Produto criado com sucesso!";
                header('Location: /tsukuyomi/public/index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao criar produto.";
                header('Location: /tsukuyomi/public/index.php?action=create_product');
            }
        }
    }
    
    // Edit product form
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: /tsukuyomi/public/index.php');
            exit();
        }
        
        $this->product->id = $id;
        $this->product->readOne();
        
        include '../views/products/edit.php';
    }
    
    // Update product
    public function update($id) {
        if($_POST) {
            $this->product->id = $id;
            $this->product->name = $_POST['name'];
            $this->product->description = $_POST['description'];
            $this->product->price = $_POST['price'];
            $this->product->category = $_POST['category'];
            $this->product->size = $_POST['size'];
            $this->product->stock_quantity = $_POST['stock_quantity'];
            $this->product->image_url = $_POST['image_url'];
            
            if($this->product->update()) {
                $_SESSION['message'] = "Produto atualizado com sucesso!";
                header('Location: /tsukuyomi/public/index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao atualizar produto.";
                header('Location: /tsukuyomi/public/index.php?action=edit_product&id=' . $id);
            }
        }
    }
    
    // Delete product
    public function destroy($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: /tsukuyomi/public/index.php');
            exit();
        }
        
        $this->product->id = $id;
        
        if($this->product->delete()) {
            $_SESSION['message'] = "Produto excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir produto.";
        }
        
        header('Location: /tsukuyomi/public/index.php?action=products');
    }
    
    // Search products
    public function search() {
        $keywords = isset($_GET['q']) ? $_GET['q'] : '';
        $stmt = $this->product->search($keywords);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/products/index.php';
    }
}
?>