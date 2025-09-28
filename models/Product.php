<?php
class Product {
    private $conn;
    private $table_name = "products";
    private $variants_table = "product_variants";
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $image_url;
    public $variants; // Armazenará os tamanhos e estoques

    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Lê um produto e suas variantes
    public function readOne() {
        // Busca o produto base
        $query = "SELECT * FROM products WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->category = $row['category'];
            $this->image_url = $row['image_url'];

            // Busca as variantes associadas na tabela product_variants
            $query_variants = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY FIELD(size, 'PP', 'P', 'M', 'G', 'GG', 'XG')";
            $stmt_variants = $this->conn->prepare($query_variants);
            $stmt_variants->bindParam(1, $this->id);
            $stmt_variants->execute();
            $this->variants = $stmt_variants->fetchAll(PDO::FETCH_ASSOC); // Popula a propriedade variants
        }
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function createWithVariants($initialStock) {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:description, price=:price, category=:category, image_url=:image_url";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":category", $this->category);
            $stmt->bindParam(":image_url", $this->image_url);
            $stmt->execute();
            $this->id = $this->conn->lastInsertId();

            $sizes = ['PP', 'P', 'M', 'G', 'GG', 'XG'];
            $query_variant = "INSERT INTO " . $this->variants_table . " SET product_id=:product_id, size=:size, stock_quantity=:stock_quantity";
            $stmt_variant = $this->conn->prepare($query_variant);

            foreach ($sizes as $size) {
                $stmt_variant->bindParam(":product_id", $this->id);
                $stmt_variant->bindParam(":size", $size);
                $stmt_variant->bindParam(":stock_quantity", $initialStock);
                $stmt_variant->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateWithVariants($stockData) {
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, price = :price, category = :category, image_url = :image_url WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':price', $this->price);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':image_url', $this->image_url);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            $query_variant = "UPDATE " . $this->variants_table . " SET stock_quantity = :stock_quantity WHERE id = :id AND product_id = :product_id";
            $stmt_variant = $this->conn->prepare($query_variant);

            foreach ($stockData as $variant_id => $quantity) {
                $stmt_variant->bindParam(':stock_quantity', $quantity);
                $stmt_variant->bindParam(':id', $variant_id);
                $stmt_variant->bindParam(':product_id', $this->id);
                $stmt_variant->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
    
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name LIKE ? OR description LIKE ? OR category LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->execute();
        return $stmt;
    }
}
?>