<?php
// adapters/DataExportAdapter.php

/**
 * Interface para o padrão Adapter de exportação de dados
 */
interface DataExportInterface {
    public function export($data, $filename);
    public function setHeaders($filename);
}

/**
 * Adapter para exportação em formato JSON
 */
class JsonExportAdapter implements DataExportInterface {
    
    public function export($data, $filename = 'export.json') {
        // Define os headers apropriados
        $this->setHeaders($filename);
        
        // Converte os dados para JSON com formatação
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // Envia o arquivo para download
        echo $jsonData;
        exit();
    }
    
    public function setHeaders($filename) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

/**
 * Adapter para exportação em formato CSV
 */
class CsvExportAdapter implements DataExportInterface {
    private $delimiter = ',';
    private $enclosure = '"';
    
    public function export($data, $filename = 'export.csv') {
        // Define os headers apropriados
        $this->setHeaders($filename);
        
        // Abre o output stream
        $output = fopen('php://output', 'w');
        
        // Adiciona BOM para UTF-8 (melhor compatibilidade com Excel)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Se há dados, processa o CSV
        if (!empty($data)) {
            // Pega os headers das colunas
            $headers = array_keys(reset($data));
            fputcsv($output, $headers, $this->delimiter, $this->enclosure);
            
            // Adiciona os dados
            foreach ($data as $row) {
                // Converte arrays e objetos para string
                $processedRow = array_map(function($value) {
                    if (is_array($value) || is_object($value)) {
                        return json_encode($value, JSON_UNESCAPED_UNICODE);
                    }
                    return $value;
                }, $row);
                
                fputcsv($output, $processedRow, $this->delimiter, $this->enclosure);
            }
        }
        
        fclose($output);
        exit();
    }
    
    public function setHeaders($filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }
    
    public function setEnclosure($enclosure) {
        $this->enclosure = $enclosure;
    }
}

/**
 * Factory para criar o adapter apropriado
 */
class DataExportFactory {
    
    public static function create($format) {
        switch(strtolower($format)) {
            case 'json':
                return new JsonExportAdapter();
            case 'csv':
                return new CsvExportAdapter();
            default:
                throw new Exception("Formato de exportação não suportado: {$format}");
        }
    }
}

/**
 * Classe principal para gerenciar exportações
 */
class DataExportManager {
    private $adapter;
    
    public function __construct(DataExportInterface $adapter) {
        $this->adapter = $adapter;
    }
    
    public function exportProducts($db) {
        $query = "SELECT p.*, 
                  (SELECT COUNT(*) FROM order_items oi WHERE oi.product_id = p.id) as total_vendas
                  FROM products p 
                  ORDER BY p.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = 'produtos_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    public function exportOrders($db, $filters = []) {
        $query = "SELECT o.*, u.name as cliente_nome, u.email as cliente_email
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id";
        
        $conditions = [];
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $conditions[] = "o.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $conditions[] = "o.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "o.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = 'pedidos_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    public function exportUsers($db, $type = null) {
        $query = "SELECT u.id, u.name, u.email, u.phone, u.city, u.state, u.user_type,
                  u.created_at,
                  (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_pedidos,
                  (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_gasto
                  FROM users u";
        
        if ($type) {
            $query .= " WHERE u.user_type = :type";
        }
        
        $query .= " ORDER BY u.created_at DESC";
        
        $stmt = $db->prepare($query);
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Remove senhas e dados sensíveis
        foreach ($data as &$user) {
            unset($user['password']);
        }
        
        $filename = 'usuarios_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    public function exportSalesReport($db, $period = 'month') {
        // Determinar o período
        $dateCondition = "";
        switch($period) {
            case 'day':
                $dateCondition = "DATE(o.created_at) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "YEARWEEK(o.created_at) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $dateCondition = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
                break;
            case 'year':
                $dateCondition = "YEAR(o.created_at) = YEAR(CURDATE())";
                break;
            default:
                $dateCondition = "1=1"; // Todos os registros
        }
        
        $query = "SELECT 
                    DATE(o.created_at) as data,
                    COUNT(DISTINCT o.id) as total_pedidos,
                    COUNT(DISTINCT o.user_id) as clientes_unicos,
                    SUM(o.total_amount) as receita_total,
                    AVG(o.total_amount) as ticket_medio,
                    GROUP_CONCAT(DISTINCT o.status) as status_pedidos
                  FROM orders o
                  WHERE {$dateCondition}
                  GROUP BY DATE(o.created_at)
                  ORDER BY data DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = 'relatorio_vendas_' . $period . '_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    public function exportCoupons($db) {
        $query = "SELECT * FROM coupons ORDER BY created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = 'cupons_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    public function exportInventory($db) {
        $query = "SELECT 
                    p.id,
                    p.name as produto,
                    p.category as categoria,
                    p.size as tamanho,
                    p.price as preco,
                    p.stock_quantity as estoque_atual,
                    COALESCE(SUM(oi.quantity), 0) as total_vendido,
                    (p.stock_quantity * p.price) as valor_estoque
                  FROM products p
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  GROUP BY p.id
                  ORDER BY p.stock_quantity ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = 'inventario_' . date('Y-m-d_H-i-s') . $this->getExtension();
        $this->adapter->export($data, $filename);
    }
    
    private function getExtension() {
        if ($this->adapter instanceof JsonExportAdapter) {
            return '.json';
        } elseif ($this->adapter instanceof CsvExportAdapter) {
            return '.csv';
        }
        return '.txt';
    }
}
?>