<?php
// views/admin/sales/dashboard.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="sales-dashboard">
    <div class="dashboard-header">
        <h1>📊 Dashboard de Vendas</h1>
        <div class="header-actions">
            <a href="index.php?action=sales_analytics" class="btn btn-primary">
                📈 Analytics Avançado
            </a>
            <a href="index.php?action=export" class="btn btn-secondary">
                💾 Exportar Dados
            </a>
        </div>
    </div>

    <!-- KPIs Principais -->
    <div class="kpi-grid">
        <div class="kpi-card today">
            <div class="kpi-header">
                <span class="kpi-title">Vendas Hoje</span>
                <span class="kpi-icon">📅</span>
            </div>
            <div class="kpi-value">R$ <?php echo number_format($metrics['today_revenue'], 2, ',', '.'); ?></div>
            <div class="kpi-detail"><?php echo $metrics['today_orders']; ?> pedidos</div>
        </div>

        <div class="kpi-card month">
            <div class="kpi-header">
                <span class="kpi-title">Vendas do Mês</span>
                <span class="kpi-icon">📆</span>
            </div>
            <div class="kpi-value">R$ <?php echo number_format($metrics['month_revenue'], 2, ',', '.'); ?></div>
            <div class="kpi-detail"><?php echo $metrics['month_orders']; ?> pedidos</div>
        </div>

        <div class="kpi-card growth">
            <div class="kpi-header">
                <span class="kpi-title">Crescimento</span>
                <span class="kpi-icon">📈</span>
            </div>
            <div class="kpi-value <?php echo $metrics['growth_rate'] >= 0 ? 'positive' : 'negative'; ?>">
                <?php echo $metrics['growth_rate'] >= 0 ? '↑' : '↓'; ?> 
                <?php echo number_format(abs($metrics['growth_rate']), 1); ?>%
            </div>
            <div class="kpi-detail">vs. mês anterior</div>
        </div>

        <div class="kpi-card ticket">
            <div class="kpi-header">
                <span class="kpi-title">Ticket Médio</span>
                <span class="kpi-icon">🎯</span>
            </div>
            <div class="kpi-value">R$ <?php echo number_format($metrics['avg_ticket'], 2, ',', '.'); ?></div>
            <div class="kpi-detail">por pedido</div>
        </div>

        <div class="kpi-card customers">
            <div class="kpi-header">
                <span class="kpi-title">Total Clientes</span>
                <span class="kpi-icon">👥</span>
            </div>
            <div class="kpi-value"><?php echo number_format($metrics['total_customers']); ?></div>
            <div class="kpi-detail">cadastrados</div>
        </div>

        <div class="kpi-card stock">
            <div class="kpi-header">
                <span class="kpi-title">Produtos em Falta</span>
                <span class="kpi-icon">⚠️</span>
            </div>
            <div class="kpi-value <?php echo $metrics['out_of_stock'] > 0 ? 'warning' : 'success'; ?>">
                <?php echo $metrics['out_of_stock']; ?>
            </div>
            <div class="kpi-detail">itens sem estoque</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Gráfico de Tendência de Vendas -->
        <div class="dashboard-card chart-card">
            <h3>📊 Tendência de Vendas (Últimos 30 dias)</h3>
            <div class="chart-wrapper">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Pedidos por Status -->
        <div class="dashboard-card status-card">
            <h3>📦 Pedidos por Status</h3>
            <div class="chart-wrapper-small">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <div class="status-legend">
                <?php foreach($ordersByStatus as $status): ?>
                    <div class="status-item">
                        <span class="status-label"><?php echo ucfirst($status['status']); ?></span>
                        <span class="status-count"><?php echo $status['count']; ?> pedidos</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Top Produtos -->
    <div class="dashboard-card">
        <h3>🏆 Top 5 Produtos Mais Vendidos</h3>
        <div class="top-products">
            <?php foreach($topProducts as $index => $product): ?>
                <div class="product-rank">
                    <span class="rank-number"><?php echo $index + 1; ?></span>
                    <img src="images/products/<?php echo $product['image_url']; ?>" 
                         alt="<?php echo $product['name']; ?>"
                         class="product-thumb">
                    <div class="product-info">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="product-stats">
                            <?php echo $product['total_sold']; ?> vendidos | 
                            R$ <?php echo number_format($product['revenue'], 2, ',', '.'); ?>
                        </p>
                    </div>
                    <div class="product-action">
                        <a href="index.php?action=product&id=<?php echo $product['id']; ?>" 
                           class="btn btn-sm btn-secondary">Ver Produto</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Pedidos Recentes -->
        <div class="dashboard-card">
            <h3>🕐 Pedidos Recentes</h3>
            <div class="recent-orders">
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?action=order&id=<?php echo $order['id']; ?>" 
                                       class="btn-link">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Produtos com Estoque Baixo -->
        <div class="dashboard-card">
            <h3>⚠️ Produtos com Estoque Baixo</h3>
            <div class="low-stock-products">
                <?php if(empty($lowStockProducts)): ?>
                    <p class="no-data">✅ Todos os produtos estão com estoque adequado!</p>
                <?php else: ?>
                    <table class="compact-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Estoque</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lowStockProducts as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo $product['category']; ?></td>
                                    <td class="stock-warning">
                                        <?php echo $product['stock_quantity']; ?> un
                                    </td>
                                    <td>
                                        <a href="index.php?action=edit_product&id=<?php echo $product['id']; ?>" 
                                           class="btn-link">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.sales-dashboard {
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary-color) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.kpi-title {
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.kpi-icon {
    font-size: 1.5rem;
}

.kpi-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.kpi-value.positive {
    color: var(--success-color);
}

.kpi-value.negative {
    color: var(--error-color);
}

.kpi-value.warning {
    color: var(--warning-color);
}

.kpi-detail {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.dashboard-card {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
}

.dashboard-card h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}

/* Chart Wrappers - IMPORTANTE: Define altura fixa para os gráficos */
.chart-wrapper {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-wrapper-small {
    position: relative;
    height: 200px;
    width: 100%;
    margin-bottom: 1rem;
}

/* Top Products */
.top-products {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.product-rank {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(15, 15, 15, 0.3);
    border-radius: var(--border-radius-lg);
    transition: all 0.3s ease;
}

.product-rank:hover {
    background: rgba(139, 92, 246, 0.1);
}

.rank-number {
    width: 30px;
    height: 30px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.product-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: var(--border-radius-md);
}

.product-info {
    flex: 1;
}

.product-info h4 {
    margin: 0 0 0.25rem 0;
    color: var(--text-primary);
}

.product-stats {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* Tables */
.compact-table {
    width: 100%;
    font-size: 0.875rem;
}

.compact-table th {
    text-align: left;
    padding: 0.75rem;
    border-bottom: 1px solid rgba(139, 92, 246, 0.2);
    color: var(--primary-color);
    font-weight: 600;
}

.compact-table td {
    padding: 0.75rem;
    border-bottom: 1px solid rgba(139, 92, 246, 0.1);
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pending {
    background: rgba(251, 191, 36, 0.1);
    color: #fbbf24;
}

.status-processing {
    background: rgba(96, 165, 250, 0.1);
    color: #60a5fa;
}

.status-shipped {
    background: rgba(167, 139, 250, 0.1);
    color: #a78bfa;
}

.status-delivered {
    background: rgba(52, 211, 153, 0.1);
    color: #34d399;
}

.status-cancelled {
    background: rgba(248, 113, 113, 0.1);
    color: #f87171;
}

.btn-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.btn-link:hover {
    text-decoration: underline;
}

.stock-warning {
    color: var(--warning-color);
    font-weight: bold;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}

/* Status Legend */
.status-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

.status-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem;
    background: rgba(15, 15, 15, 0.3);
    border-radius: var(--border-radius-sm);
}

/* Scrollbar para tabelas com overflow */
.recent-orders {
    max-height: 400px;
    overflow-y: auto;
}

.low-stock-products {
    max-height: 400px;
    overflow-y: auto;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .kpi-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .product-rank {
        flex-wrap: wrap;
    }
    
    .chart-wrapper {
        height: 250px;
    }
    
    .chart-wrapper-small {
        height: 150px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Aguardar o DOM carregar completamente
document.addEventListener('DOMContentLoaded', function() {
    // Preparar dados para o gráfico de tendência
    const salesTrendData = <?php echo json_encode($salesTrend); ?>;
    const trendLabels = salesTrendData.map(item => {
        const date = new Date(item.date);
        return date.getDate() + '/' + (date.getMonth() + 1);
    });
    const trendRevenue = salesTrendData.map(item => parseFloat(item.revenue));

    // Gráfico de Tendência de Vendas
    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Receita',
                data: trendRevenue,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });

    // Preparar dados para o gráfico de status
    const orderStatusData = <?php echo json_encode($ordersByStatus); ?>;
    const statusLabels = orderStatusData.map(item => {
        const labels = {
            'pending': 'Pendente',
            'processing': 'Processando',
            'shipped': 'Enviado',
            'delivered': 'Entregue',
            'cancelled': 'Cancelado'
        };
        return labels[item.status] || item.status;
    });
    const statusCounts = orderStatusData.map(item => item.count);

    // Gráfico de Pedidos por Status
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: [
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(96, 165, 250, 0.8)',
                    'rgba(167, 139, 250, 0.8)',
                    'rgba(52, 211, 153, 0.8)',
                    'rgba(248, 113, 113, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php include '../views/layout/footer.php'; ?>