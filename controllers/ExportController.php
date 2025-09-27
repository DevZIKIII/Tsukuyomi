<?php
// controllers/ExportController.php

require_once '../adapters/DataExportAdapter.php';
require_once '../config/database.php';

class ExportController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Página principal de exportação (admin)
     */
    public function index() {
        // Verificar se é admin
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        include '../views/admin/export/index.php';
    }
    
    /**
     * Processar exportação baseada nos parâmetros
     */
    public function process() {
        // Verificar se é admin
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        try {
            // Obter parâmetros
            $type = $_GET['type'] ?? 'orders';
            $format = $_GET['format'] ?? 'csv';
            
            // Criar o adapter apropriado usando a factory
            $adapter = DataExportFactory::create($format);
            
            // Criar o gerenciador de exportação
            $exportManager = new DataExportManager($adapter);
            
            // Processar baseado no tipo de dados
            switch($type) {
                case 'products':
                    $exportManager->exportProducts($this->db);
                    break;
                    
                case 'orders':
                    $filters = [
                        'status' => $_GET['status'] ?? null,
                        'date_from' => $_GET['date_from'] ?? null,
                        'date_to' => $_GET['date_to'] ?? null
                    ];
                    $exportManager->exportOrders($this->db, $filters);
                    break;
                    
                case 'users':
                    $userType = $_GET['user_type'] ?? null;
                    $exportManager->exportUsers($this->db, $userType);
                    break;
                    
                case 'sales':
                    $period = $_GET['period'] ?? 'month';
                    $exportManager->exportSalesReport($this->db, $period);
                    break;
                    
                case 'coupons':
                    $exportManager->exportCoupons($this->db);
                    break;
                    
                case 'inventory':
                    $exportManager->exportInventory($this->db);
                    break;
                    
                case 'custom':
                    $this->exportCustom($adapter);
                    break;
                    
                default:
                    throw new Exception("Tipo de exportação inválido");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erro ao exportar dados: " . $e->getMessage();
            header('Location: index.php?action=export');
            exit();
        }
    }
    
    /**
     * Exportação customizada com query SQL
     */
    private function exportCustom($adapter) {
        if (!isset($_POST['query']) || empty($_POST['query'])) {
            throw new Exception("Query SQL não fornecida");
        }
        
        $query = $_POST['query'];
        
        // Validação básica de segurança - apenas SELECT permitido
        if (!preg_match('/^SELECT/i', trim($query))) {
            throw new Exception("Apenas queries SELECT são permitidas");
        }
        
        // Verificar palavras perigosas
        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'ALTER', 'CREATE', 'TRUNCATE'];
        foreach ($dangerousKeywords as $keyword) {
            if (stripos($query, $keyword) !== false) {
                throw new Exception("Query contém comando não permitido: {$keyword}");
            }
        }
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $exportManager = new DataExportManager($adapter);
            $filename = 'custom_export_' . date('Y-m-d_H-i-s') . ($adapter instanceof JsonExportAdapter ? '.json' : '.csv');
            $adapter->export($data, $filename);
            
        } catch (PDOException $e) {
            throw new Exception("Erro na query SQL: " . $e->getMessage());
        }
    }
    
    /**
     * Gerar relatório de vendas detalhado
     */
    public function salesReport() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        // Obter estatísticas gerais
        $stats = $this->getSalesStatistics();
        
        // Obter vendas por produto
        $productSales = $this->getProductSales();
        
        // Obter vendas por categoria
        $categorySales = $this->getCategorySales();
        
        // Obter clientes top
        $topCustomers = $this->getTopCustomers();
        
        include '../views/admin/export/sales_report.php';
    }
    
    private function getSalesStatistics() {
        $query = "SELECT 
                    COUNT(DISTINCT o.id) as total_orders,
                    COUNT(DISTINCT o.user_id) as total_customers,
                    SUM(o.total_amount) as total_revenue,
                    AVG(o.total_amount) as avg_order_value,
                    MAX(o.total_amount) as max_order_value,
                    MIN(o.total_amount) as min_order_value
                  FROM orders o
                  WHERE o.status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getProductSales() {
        $query = "SELECT 
                    p.id,
                    p.name,
                    p.category,
                    p.price,
                    COUNT(oi.id) as times_sold,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue
                  FROM products p
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  LEFT JOIN orders o ON oi.order_id = o.id
                  WHERE o.status != 'cancelled' OR o.status IS NULL
                  GROUP BY p.id
                  ORDER BY total_revenue DESC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getCategorySales() {
        $query = "SELECT 
                    p.category,
                    COUNT(DISTINCT p.id) as total_products,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.quantity * oi.price) as total_revenue
                  FROM products p
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  LEFT JOIN orders o ON oi.order_id = o.id
                  WHERE o.status != 'cancelled' OR o.status IS NULL
                  GROUP BY p.category
                  ORDER BY total_revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTopCustomers() {
        $query = "SELECT 
                    u.id,
                    u.name,
                    u.email,
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as total_spent,
                    MAX(o.created_at) as last_order_date
                  FROM users u
                  INNER JOIN orders o ON u.id = o.user_id
                  WHERE o.status != 'cancelled'
                  GROUP BY u.id
                  ORDER BY total_spent DESC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>