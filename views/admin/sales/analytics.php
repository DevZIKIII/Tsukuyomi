<?php
// views/admin/sales/analytics.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="analytics-container">
    <div class="analytics-header">
        <h1>📈 Analytics Avançado de Vendas</h1>
        <a href="index.php?action=sales_dashboard" class="btn btn-secondary">
            ← Voltar ao Dashboard
        </a>
    </div>

    <!-- Filtros de Período -->
    <div class="filters-section">
        <form method="GET" action="index.php" class="period-filter">
            <input type="hidden" name="action" value="sales_analytics">
            <div class="filter-group">
                <label for="start_date">Data Inicial:</label>
                <input type="date" id="start_date" name="start_date" 
                       value="<?php echo htmlspecialchars($_GET['start_date'] ?? date('Y-m-01')); ?>"
                       class="form-control">
            </div>
            <div class="filter-group">
                <label for="end_date">Data Final:</label>
                <input type="date" id="end_date" name="end_date" 
                       value="<?php echo htmlspecialchars($_GET['end_date'] ?? date('Y-m-t')); ?>"
                       class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Aplicar Filtros</button>
        </form>
    </div>

    <!-- Métricas de Cliente -->
    <div class="analytics-section">
        <h2>👥 Analytics de Clientes</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Novos Clientes</div>
                <div class="metric-value"><?php echo $customerAnalytics['new_customers']; ?></div>
                <div class="metric-detail">no período</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Clientes Ativos</div>
                <div class="metric-value"><?php echo $customerAnalytics['active_customers']; ?></div>
                <div class="metric-detail">compraram no período</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Taxa de Retenção</div>
                <div class="metric-value"><?php echo number_format($customerAnalytics['retention_rate'], 1); ?>%</div>
                <div class="metric-detail">clientes recorrentes</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">LTV Médio</div>
                <div class="metric-value">R$ <?php echo number_format($customerAnalytics['avg_ltv'], 2, ',', '.'); ?></div>
                <div class="metric-detail">lifetime value</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Taxa de Conversão</div>
                <div class="metric-value"><?php echo number_format($conversionRate, 1); ?>%</div>
                <div class="metric-detail">carrinho → pedido</div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Vendas por Dia -->
    <div class="analytics-section">
        <h2>📊 Vendas por Dia</h2>
        <div class="chart-container">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <div class="analytics-grid">
        <!-- Vendas por Categoria -->
        <div class="analytics-section">
            <h2>📂 Vendas por Categoria</h2>
            <div class="chart-container">
                <canvas id="categorySalesChart"></canvas>
            </div>
            <div class="category-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Pedidos</th>
                            <th>Itens</th>
                            <th>Receita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($salesByCategory as $category): ?>
                            <tr>
                                <td><?php echo $category['category']; ?></td>
                                <td><?php echo $category['orders']; ?></td>
                                <td><?php echo $category['items_sold']; ?></td>
                                <td>R$ <?php echo number_format($category['revenue'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vendas por Método de Pagamento -->
        <div class="analytics-section">
            <h2>💳 Vendas por Método de Pagamento</h2>
            <div class="chart-container">
                <canvas id="paymentMethodChart"></canvas>
            </div>
            <div class="payment-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Pedidos</th>
                            <th>Receita</th>
                            <th>% Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalRevenue = array_sum(array_column($salesByPaymentMethod, 'revenue'));
                        foreach($salesByPaymentMethod as $payment): 
                            $percentage = $totalRevenue > 0 ? ($payment['revenue'] / $totalRevenue) * 100 : 0;
                        ?>
                            <tr>
                                <td>
                                    <?php 
                                    $labels = [
                                        'card' => '💳 Cartão',
                                        'pix' => '📱 PIX',
                                        'boleto' => '📄 Boleto'
                                    ];
                                    echo $labels[$payment['payment_method']] ?? ucfirst($payment['payment_method']);
                                    ?>
                                </td>
                                <td><?php echo $payment['orders']; ?></td>
                                <td>R$ <?php echo number_format($payment['revenue'], 2, ',', '.'); ?></td>
                                <td><?php echo number_format($percentage, 1); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance de Produtos -->
    <div class="analytics-section">
        <h2>🏆 Performance de Produtos</h2>
        <div class="performance-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço Atual</th>
                        <th>Preço Médio Venda</th>
                        <th>Unidades Vendidas</th>
                        <th>Receita</th>
                        <th>Estoque</th>
                        <th>Giro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productPerformance as $product): ?>
                        <?php 
                        $turnover = $product['stock_quantity'] > 0 ? 
                                   $product['units_sold'] / ($product['units_sold'] + $product['stock_quantity']) * 100 : 
                                   100;
                        ?>
                        <tr>
                            <td>
                                <a href="index.php?action=product&id=<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </td>
                            <td><?php echo $product['category']; ?></td>
                            <td>R$ <?php echo number_format($product['current_price'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($product['avg_selling_price'], 2, ',', '.'); ?></td>
                            <td><?php echo $product['units_sold']; ?></td>
                            <td>R$ <?php echo number_format($product['revenue'], 2, ',', '.'); ?></td>
                            <td class="<?php echo $product['stock_quantity'] < 10 ? 'stock-low' : ''; ?>">
                                <?php echo $product['stock_quantity']; ?>
                            </td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $turnover; ?>%"></div>
                                </div>
                                <small><?php echo number_format($turnover, 1); ?>%</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ações de Exportação -->
    <div class="export-section">
        <h3>💾 Exportar Dados de Analytics</h3>
        <div class="export-buttons">
            <button onclick="exportAnalytics('csv')" class="btn btn-secondary">
                📄 Exportar CSV
            </button>
            <button onclick="exportAnalytics('json')" class="btn btn-secondary">
                📋 Exportar JSON
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Imprimir Relatório
            </button>
        </div>
    </div>
</div>

<style>
.analytics-container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.analytics-header h1 {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary-color) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Filtros */
.filters-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    margin-bottom: 2rem;
}

.period-filter {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Métricas */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.metric-card {
    background: rgba(15, 15, 15, 0.5);
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    border: 1px solid rgba(139, 92, 246, 0.1);
    text-align: center;
}

.metric-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.metric-detail {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

/* Seções */
.analytics-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    margin-bottom: 2rem;
}

.analytics-section h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}

.analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

/* Gráficos */
.chart-container {
    height: 300px;
    margin-bottom: 1.5rem;
}

/* Tabelas */
.data-table {
    width: 100%;
    font-size: 0.875rem;
}

.data-table th {
    text-align: left;
    padding: 0.75rem;
    border-bottom: 2px solid rgba(139, 92, 246, 0.2);
    color: var(--primary-color);
    font-weight: 600;
}

.data-table td {
    padding: 0.75rem;
    border-bottom: 1px solid rgba(139, 92, 246, 0.1);
}

.data-table a {
    color: var(--primary-color);
    text-decoration: none;
}

.data-table a:hover {
    text-decoration: underline;
}

.stock-low {
    color: var(--warning-color);
    font-weight: bold;
}

/* Progress Bar */
.progress-bar {
    width: 100px;
    height: 6px;
    background: rgba(139, 92, 246, 0.1);
    border-radius: 3px;
    overflow: hidden;
    display: inline-block;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), #a78bfa);
}

/* Export Section */
.export-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    text-align: center;
}

.export-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1rem;
}

@media (max-width: 1024px) {
    .analytics-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .period-filter {
        flex-direction: column;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media print {
    .analytics-header a,
    .filters-section,
    .export-section {
        display: none;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dados para os gráficos
const salesByDayData = <?php echo json_encode($salesByDay); ?>;
const salesByCategoryData = <?php echo json_encode($salesByCategory); ?>;
const salesByPaymentData = <?php echo json_encode($salesByPaymentMethod); ?>;

// Gráfico de Vendas por Dia
const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: salesByDayData.map(item => {
            const date = new Date(item.date);
            return date.getDate() + '/' + (date.getMonth() + 1);
        }),
        datasets: [{
            label: 'Receita Diária',
            data: salesByDayData.map(item => parseFloat(item.revenue)),
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: '#8b5cf6',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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

// Gráfico de Vendas por Categoria
const categoryCtx = document.getElementById('categorySalesChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: salesByCategoryData.map(item => item.category),
        datasets: [{
            data: salesByCategoryData.map(item => parseFloat(item.revenue)),
            backgroundColor: [
                'rgba(139, 92, 246, 0.8)',
                'rgba(167, 139, 250, 0.8)',
                'rgba(196, 181, 253, 0.8)',
                'rgba(221, 214, 254, 0.8)',
                'rgba(237, 233, 254, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de Métodos de Pagamento
const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: salesByPaymentData.map(item => {
            const labels = {
                'card': 'Cartão',
                'pix': 'PIX',
                'boleto': 'Boleto'
            };
            return labels[item.payment_method] || item.payment_method;
        }),
        datasets: [{
            data: salesByPaymentData.map(item => parseFloat(item.revenue)),
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(251, 191, 36, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Função de exportação
function exportAnalytics(format) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    window.location.href = `index.php?action=export_data&type=sales&period=custom&start_date=${startDate}&end_date=${endDate}&format=${format}`;
}
</script>

<?php include '../views/layout/footer.php'; ?>