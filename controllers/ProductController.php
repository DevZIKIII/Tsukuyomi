<?php
require_once '../factories/ModelFactory.php';

class ProductController {
    private $productFactory;
    
    public function __construct() {
        $factoryManager = FactoryManager::getInstance();
        $this->productFactory = $factoryManager->getFactory('product');
    }
    
    public function index() {
        $product = $this->productFactory->createModel();
        $stmt = $product->read();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include '../views/products/index.php';
    }
    
    public function show($id) {
        $product = $this->productFactory->createModel();
        $product->id = $id;
        $product->readOne();
        
        if(!$product->name) {
            $_SESSION['error'] = "Produto não encontrado.";
            header('Location: index.php?action=products');
            exit();
        }
        
        include '../views/products/show.php';
    }
    
    public function create() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php'); exit();
        }
        include '../views/products/create.php';
    }
    
    public function store() {
        if($_POST) {
            $product = $this->productFactory->createModel();
            $product->name = $_POST['name'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->category = $_POST['category'];

            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $uploadDir = '../public/images/products/';
                $fileName = uniqid() . '-' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
                    $product->image_url = $fileName;
                }
            }
            
            $initialStock = $_POST['stock_quantity'] ?? 0;
            if($product->createWithVariants($initialStock)) {
                $_SESSION['message'] = "Produto criado com sucesso!";
                header('Location: index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao criar produto.";
                header('Location: index.php?action=create_product');
            }
        }
    }
    
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php'); exit();
        }
        
        $product = $this->productFactory->createModel();
        $product->id = $id;
        $product->readOne();
        
        if(!$product->name) {
            $_SESSION['error'] = "Produto não encontrado.";
            header('Location: index.php?action=products'); exit();
        }
        
        include '../views/products/edit.php';
    }
    
    public function update($id) {
        if ($_POST) {
            $product = $this->productFactory->createModel();
            $product->id = $id;
            $product->name = $_POST['name'];
            $product->description = $_POST['description'];
            $product->price = $_POST['price'];
            $product->category = $_POST['category'];
            $product->image_url = $_POST['current_image_url'];

            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                 $uploadDir = '../public/images/products/';
                 $fileName = uniqid() . '-' . basename($_FILES['image_file']['name']);
                 if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
                      $product->image_url = $fileName;
                 }
            }

            $stockData = $_POST['stock'] ?? [];
            if ($product->updateWithVariants($stockData)) {
                $_SESSION['message'] = "Produto atualizado com sucesso!";
                header('Location: index.php?action=products');
            } else {
                $_SESSION['error'] = "Erro ao atualizar produto.";
                header('Location: index.php?action=edit_product&id=' . $id);
            }
        }
    }
    
    public function destroy($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php'); exit();
        }
        
        $product = $this->productFactory->createModel();
        $product->id = $id;
        
        if($product->delete()) {
            $_SESSION['message'] = "Produto e suas variantes foram excluídos.";
        } else {
            $_SESSION['error'] = "Erro ao excluir produto.";
        }
        
        header('Location: index.php?action=products');
    }

    public function search() {
        $keywords = $_GET['q'] ?? '';
        $product = $this->productFactory->createModel();
        $stmt = $product->search($keywords);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include '../views/products/index.php';
    }
}
?>