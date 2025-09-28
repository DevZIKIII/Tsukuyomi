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
    
    public function dashboard() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php'); exit();
        }
        
        $metrics = $this->getDashboardMetrics();
        $recentOrders = $this->getRecentOrders(10);
        $salesTrend = $this->getSalesTrend(30);
        $topProducts = $this->getTopSellingProducts(5);
        $lowStockProducts = $this->getLowStockProducts();
        $ordersByStatus = $this->getOrdersByStatus();
        
        include '../views/admin/sales/dashboard.php';
    }
    
    public function analytics() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php'); exit();
        }
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        $salesByDay = $this->getSalesByDay($startDate, $endDate);
        $salesByCategory = $this->getSalesByCategory($startDate, $endDate);
        $salesByPaymentMethod = $this->getSalesByPaymentMethod($startDate, $endDate);
        $customerAnalytics = $this->getCustomerAnalytics($startDate, $endDate);
        $productPerformance = $this->getProductPerformance($startDate, $endDate);
        $conversionRate = $this->getConversionRate($startDate, $endDate);
        
        include '../views/admin/sales/analytics.php';
    }
    
    private function getDashboardMetrics() {
        $metrics = [];
        $query = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $today = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['today_orders'] = $today['count'];
        $metrics['today_revenue'] = $today['total'];
        
        $query = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $month = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['month_orders'] = $month['count'];
        $metrics['month_revenue'] = $month['total'];
        
        $query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $lastMonthTotal = $stmt->fetchColumn();
        
        $metrics['growth_rate'] = ($lastMonthTotal > 0) ? (($month['total'] - $lastMonthTotal) / $lastMonthTotal) * 100 : ($month['total'] > 0 ? 100 : 0);
        $metrics['avg_ticket'] = ($month['count'] > 0) ? ($month['total'] / $month['count']) : 0;
        
        $query = "SELECT COUNT(id) as count FROM users WHERE user_type = 'customer'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $metrics['total_customers'] = $stmt->fetchColumn();
        
        $query = "SELECT COUNT(p.id) FROM products p WHERE (SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id) = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $metrics['out_of_stock'] = $stmt->fetchColumn();
        
        return $metrics;
    }
    
    private function getRecentOrders($limit = 10) {
        $query = "SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSalesTrend($days = 30) {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY) AND status != 'cancelled' GROUP BY DATE(created_at) ORDER BY date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTopSellingProducts($limit = 5) {
        $query = "SELECT p.id, p.name, p.price, p.image_url, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue FROM order_items oi INNER JOIN products p ON oi.product_id = p.id INNER JOIN orders o ON oi.order_id = o.id WHERE o.status != 'cancelled' GROUP BY p.id ORDER BY total_sold DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getLowStockProducts($threshold = 10) {
        $query = "SELECT p.id, p.name, p.category, SUM(pv.stock_quantity) as total_stock FROM products p JOIN product_variants pv ON p.id = pv.product_id GROUP BY p.id, p.name, p.category HAVING total_stock < :threshold AND total_stock > 0 ORDER BY total_stock ASC LIMIT 10";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getOrdersByStatus() {
        $query = "SELECT status, COUNT(*) as count, SUM(total_amount) as total FROM orders GROUP BY status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getSalesByDay($startDate, $endDate) {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue FROM orders WHERE DATE(created_at) BETWEEN :start_date AND :end_date AND status != 'cancelled' GROUP BY DATE(created_at) ORDER BY date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSalesByCategory($startDate, $endDate) {
        $query = "SELECT p.category, COUNT(DISTINCT oi.order_id) as orders, SUM(oi.quantity) as items_sold, SUM(oi.quantity * oi.price) as revenue FROM order_items oi INNER JOIN products p ON oi.product_id = p.id INNER JOIN orders o ON oi.order_id = o.id WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date AND o.status != 'cancelled' GROUP BY p.category ORDER BY revenue DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSalesByPaymentMethod($startDate, $endDate) {
        $query = "SELECT payment_method, COUNT(*) as orders, SUM(total_amount) as revenue FROM orders WHERE DATE(created_at) BETWEEN :start_date AND :end_date AND status != 'cancelled' GROUP BY payment_method ORDER BY revenue DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // CORREÇÃO: Cálculos de retenção e LTV adicionados
    private function getCustomerAnalytics($startDate, $endDate) {
        $analytics = [];
        
        $query_new = "SELECT COUNT(*) FROM users WHERE DATE(created_at) BETWEEN :start_date AND :end_date AND user_type = 'customer'";
        $stmt_new = $this->db->prepare($query_new);
        $stmt_new->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        $analytics['new_customers'] = $stmt_new->fetchColumn();
        
        $query_active = "SELECT COUNT(DISTINCT user_id) FROM orders WHERE DATE(created_at) BETWEEN :start_date AND :end_date AND status != 'cancelled'";
        $stmt_active = $this->db->prepare($query_active);
        $stmt_active->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        $analytics['active_customers'] = $stmt_active->fetchColumn();
        
        $query_retention = "SELECT COUNT(DISTINCT user_id) as returning_customers, (SELECT COUNT(DISTINCT user_id) FROM orders WHERE DATE(created_at) BETWEEN :start_date AND :end_date) as total_customers FROM (SELECT user_id FROM orders WHERE DATE(created_at) BETWEEN :start_date AND :end_date GROUP BY user_id HAVING COUNT(id) > 1) as returning_query";
        $stmt_retention = $this->db->prepare($query_retention);
        $stmt_retention->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        $retention_data = $stmt_retention->fetch(PDO::FETCH_ASSOC);
        $analytics['retention_rate'] = ($retention_data['total_customers'] > 0) ? ($retention_data['returning_customers'] / $retention_data['total_customers']) * 100 : 0;

        $query_ltv = "SELECT AVG(customer_total) as avg_ltv FROM (SELECT user_id, SUM(total_amount) as customer_total FROM orders WHERE status != 'cancelled' GROUP BY user_id) as customer_totals";
        $stmt_ltv = $this->db->prepare($query_ltv);
        $stmt_ltv->execute();
        $analytics['avg_ltv'] = $stmt_ltv->fetchColumn() ?: 0;
        
        return $analytics;
    }
    
    private function getProductPerformance($startDate, $endDate) {
        $query = "SELECT p.id, p.name, p.category, p.price as current_price, (SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id) as stock_quantity, COALESCE(SUM(oi.quantity), 0) as units_sold, COALESCE(SUM(oi.quantity * oi.price), 0) as revenue, COALESCE(AVG(oi.price), p.price) as avg_selling_price FROM products p LEFT JOIN order_items oi ON p.id = oi.product_id AND oi.order_id IN (SELECT o.id FROM orders o WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date AND o.status != 'cancelled') GROUP BY p.id ORDER BY revenue DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getConversionRate($startDate, $endDate) {
        return 0;
    }
}
?>