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
        $today = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['today_orders'] = $today['count'];
        $metrics['today_revenue'] = $today['total'];
        
        // Vendas do mês
        $query = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total 
                  FROM orders 
                  WHERE MONTH(created_at) = MONTH(CURDATE()) 
                  AND YEAR(created_at) = YEAR(CURDATE())
                  AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $month = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['month_orders'] = $month['count'];
        $metrics['month_revenue'] = $month['total'];
        
        // Comparação com mês anterior
        $query = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total 
                  FROM orders 
                  WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                  AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                  AND status != 'cancelled'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $lastMonth = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calcular crescimento
        if ($lastMonth['total'] > 0) {
            $metrics['growth_rate'] = (($month['total'] - $lastMonth['total']) / $lastMonth['total']) * 100;
        } else {
            $metrics['growth_rate'] = 100;
        }
        
        // Ticket médio
        if ($month['count'] > 0) {
            $metrics['avg_ticket'] = $month['total'] / $month['count'];
        } else {
            $metrics['avg_ticket'] = 0;
        }
        
        // Total de clientes
        $query = "SELECT COUNT(DISTINCT user_id) as count FROM orders";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $customers = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['total_customers'] = $customers['count'];
        
        // Produtos em falta
        $query = "SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $outOfStock = $stmt->fetch(PDO::FETCH_ASSOC);
        $metrics['out_of_stock'] = $outOfStock['count'];
        
        return $metrics;
    }
    
    /**
     * Obter pedidos recentes
     */
    private function getRecentOrders($limit = 10) {
        $query = "SELECT o.*, u.name as customer_name 
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter tendência de vendas
     */
    private function getSalesTrend($days = 30) {
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue
                  FROM orders
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  AND status != 'cancelled'
                  GROUP BY DATE(created_at)
                  ORDER BY date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter produtos mais vendidos
     */
    private function getTopSellingProducts($limit = 5) {
        $query = "SELECT 
                    p.id,
                    p.name,
                    p.price,
                    p.image_url,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.quantity * oi.price) as revenue
                  FROM order_items oi
                  INNER JOIN products p ON oi.product_id = p.id
                  INNER JOIN orders o ON oi.order_id = o.id
                  WHERE o.status != 'cancelled'
                  GROUP BY p.id
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter produtos com estoque baixo
     */
    private function getLowStockProducts($threshold = 10) {
        $query = "SELECT * FROM products 
                  WHERE stock_quantity < :threshold 
                  AND stock_quantity > 0
                  ORDER BY stock_quantity ASC
                  LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter pedidos por status
     */
    private function getOrdersByStatus() {
        $query = "SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(total_amount) as total
                  FROM orders
                  GROUP BY status";
        
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
                    COUNT(DISTINCT CASE WHEN order_count > 1 THEN user_id END) as returning_customers,
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
            $analytics['retention_rate'] = ($result['returning_customers'] / $result['total']) * 100;
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
    
    /**
     * Obter vendas por hora do dia (para análise de picos)
     */
    private function getSalesByHour($startDate, $endDate) {
        $query = "SELECT 
                    HOUR(created_at) as hour,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue
                  FROM orders
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date
                  AND status != 'cancelled'
                  GROUP BY HOUR(created_at)
                  ORDER BY hour";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter estatísticas de cupons
     */
    public function getCouponStatistics($startDate = null, $endDate = null) {
        $query = "SELECT 
                    c.code,
                    c.description,
                    c.discount_type,
                    c.discount_value,
                    c.used_count,
                    c.usage_limit,
                    COUNT(o.id) as orders_with_coupon,
                    SUM(o.total_amount) as total_sales_with_coupon
                  FROM coupons c
                  LEFT JOIN orders o ON o.id IN (
                    SELECT order_id FROM order_coupons WHERE coupon_id = c.id
                  )";
        
        if ($startDate && $endDate) {
            $query .= " WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date";
        }
        
        $query .= " GROUP BY c.id
                   ORDER BY orders_with_coupon DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($startDate && $endDate) {
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter média de itens por pedido
     */
    private function getAverageItemsPerOrder($startDate, $endDate) {
        $query = "SELECT 
                    AVG(item_count) as avg_items_per_order
                  FROM (
                    SELECT o.id, COUNT(oi.id) as item_count
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
                    AND o.status != 'cancelled'
                    GROUP BY o.id
                  ) as order_items_count";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_items_per_order'] ?: 0;
    }
    
    /**
     * Obter estatísticas de cancelamento
     */
    private function getCancellationStats($startDate, $endDate) {
        $query = "SELECT 
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
                    COUNT(*) as total_orders,
                    (COUNT(CASE WHEN status = 'cancelled' THEN 1 END) * 100.0 / COUNT(*)) as cancellation_rate,
                    SUM(CASE WHEN status = 'cancelled' THEN total_amount ELSE 0 END) as lost_revenue
                  FROM orders
                  WHERE DATE(created_at) BETWEEN :start_date AND :end_date";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obter previsão de vendas (baseada em média móvel simples)
     */
    public function getSalesForecast($days = 7) {
        // Pegar dados dos últimos 30 dias para calcular tendência
        $query = "SELECT 
                    DATE(created_at) as date,
                    SUM(total_amount) as daily_revenue
                  FROM orders
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                  AND status != 'cancelled'
                  GROUP BY DATE(created_at)
                  ORDER BY date";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $historicalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular média móvel
        $avgRevenue = 0;
        if (count($historicalData) > 0) {
            $totalRevenue = array_sum(array_column($historicalData, 'daily_revenue'));
            $avgRevenue = $totalRevenue / count($historicalData);
        }
        
        // Projetar próximos dias
        $forecast = [];
        for ($i = 1; $i <= $days; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $forecast[] = [
                'date' => $date,
                'projected_revenue' => $avgRevenue * (1 + (rand(-10, 10) / 100)) // Adiciona variação de ±10%
            ];
        }
        
        return $forecast;
    }
}
?>