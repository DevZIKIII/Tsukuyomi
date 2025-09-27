<?php
// controllers/SalesController.php

require_once '../config/database.php';
require_once '../adapters/DataExportAdapter.php';

class SalesController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Dashboard principal de vendas
     */
    public function dashboard() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        // Obter métricas do dashboard
        $metrics = $this->getDashboardMetrics();
        $recentOrders = $this->getRecentOrders(10);
        $salesTrend = $this->getSalesTrend(30); // Últimos 30 dias
        $topProducts = $this->getTopSellingProducts(5);
        $lowStockProducts = $this->getLowStockProducts();
        $ordersByStatus = $this->getOrdersByStatus();
        
        include '../views/admin/sales/dashboard.php';
    }
    
    /**
     * Analytics avançado de vendas
     */
    public function analytics() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
        
        // Período de análise (padrão: último mês)
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        // Análises
        $salesByDay = $this->getSalesByDay($startDate, $endDate);
        $salesByCategory = $this->getSalesByCategory($startDate, $endDate);
        $salesByPaymentMethod = $this->getSalesByPaymentMethod($startDate, $endDate);
        $customerAnalytics = $this->getCustomerAnalytics($startDate, $endDate);
        $productPerformance = $this->getProductPerformance($startDate, $endDate);
        $conversionRate = $this->getConversionRate($startDate, $endDate);
        
        include '../views/admin/sales/analytics.php';
    }
    
    /**
     * Obter métricas principais do dashboard
     */
    private function getDashboardMetrics() {
        $metrics = [];
        
        // Vendas de hoje
        $query = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total 
                  FROM orders 
                  WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter vendas por dia
     */
    private function getSalesByDay($startDate, $endDate) {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue
                  FROM orders
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                  AND status != 'cancelled'
                  GROUP BY DATE(created_at)
                  ORDER BY date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter vendas por categoria
     */
    private function getSalesByCategory($startDate, $endDate) {
        $query = "SELECT 
                    p.category,
                    COUNT(DISTINCT oi.order_id) as orders,
                    SUM(oi.quantity) as items_sold,
                    SUM(oi.quantity * oi.price) as revenue
                  FROM order_items oi
                  INNER JOIN products p ON oi.product_id = p.id
                  INNER JOIN orders o ON oi.order_id = o.id
                  WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                  AND o.status != 'cancelled'
                  GROUP BY p.category
                  ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter vendas por método de pagamento
     */
    private function getSalesByPaymentMethod($startDate, $endDate) {
        $query = "SELECT 
                    payment_method,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue
                  FROM orders
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                  AND status != 'cancelled'
                  GROUP BY payment_method
                  ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Análise de clientes
     */
    private function getCustomerAnalytics($startDate, $endDate) {
        $analytics = [];
        
        // Novos clientes no período
        $query = "SELECT COUNT(*) as count 
                  FROM users 
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                  AND user_type = 'customer'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $analytics['new_customers'] = $result['count'];
        
        // Clientes que compraram no período
        $query = "SELECT COUNT(DISTINCT user_id) as count 
                  FROM orders 
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                  AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $analytics['active_customers'] = $result['count'];
        
        // Taxa de recompra
        $query = "SELECT 
                    COUNT(DISTINCT CASE WHEN order_count > 1 THEN user_id END) as returning,
                    COUNT(DISTINCT user_id) as total
                  FROM (
                    SELECT user_id, COUNT(*) as order_count
                    FROM orders
                    WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                    AND status != 'cancelled'
                    GROUP BY user_id
                  ) as user_orders";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            $analytics['retention_rate'] = ($result['returning'] / $result['total']) * 100;
        } else {
            $analytics['retention_rate'] = 0;
        }
        
        // Lifetime value médio
        $query = "SELECT AVG(customer_total) as avg_ltv
                  FROM (
                    SELECT user_id, SUM(total_amount) as customer_total
                    FROM orders
                    WHERE status != 'cancelled'
                    GROUP BY user_id
                  ) as customer_totals";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $analytics['avg_ltv'] = $result['avg_ltv'] ?: 0;
        
        return $analytics;
    }
    
    /**
     * Performance de produtos
     */
    private function getProductPerformance($startDate, $endDate) {
        $query = "SELECT 
                    p.id,
                    p.name,
                    p.category,
                    p.price as current_price,
                    p.stock_quantity,
                    COALESCE(SUM(oi.quantity), 0) as units_sold,
                    COALESCE(SUM(oi.quantity * oi.price), 0) as revenue,
                    COALESCE(AVG(oi.price), p.price) as avg_selling_price
                  FROM products p
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  LEFT JOIN orders o ON oi.order_id = o.id 
                    AND DATE(o.created_at) BETWEEN :start_date AND :end_date
                    AND o.status != 'cancelled'
                  GROUP BY p.id
                  ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Taxa de conversão
     */
    private function getConversionRate($startDate, $endDate) {
        // Para calcular taxa de conversão real, precisaríamos de dados de visitas
        // Por enquanto, vamos simular com base em carrinho abandonado
        
        $query = "SELECT 
                    COUNT(DISTINCT c.user_id) as cart_users,
                    COUNT(DISTINCT o.user_id) as order_users
                  FROM cart_items c
                  LEFT JOIN orders o ON c.user_id = o.user_id 
                    AND DATE(o.created_at) BETWEEN :start_date AND :end_date
                    AND o.status != 'cancelled'
                  WHERE DATE(c.created_at) BETWEEN :start_date2 AND :end_date2";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':start_date2', $startDate);
        $stmt->bindParam(':end_date2', $endDate);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['cart_users'] > 0) {
            return ($result['order_users'] / $result['cart_users']) * 100;
        }
        
        return 0;
    }
}
?>