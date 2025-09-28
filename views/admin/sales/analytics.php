<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📈 Analytics Avançado de Vendas</h1>
        <a href="index.php?action=sales_dashboard" class="btn btn-secondary">
            ← Voltar ao Dashboard
        </a>
    </div>

    <div class="dashboard-card filters-section">
        <form method="GET" action="index.php" class="period-filter">
            <input type="hidden" name="action" value="sales_analytics">
            <div class="filter-group">
                <label for="start_date">Data Inicial:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? date('Y-m-01')); ?>" class="form-control">
            </div>
            <div class="filter-group">
                <label for="end_date">Data Final:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? date('Y-m-t')); ?>" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Aplicar Filtros</button>
        </form>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Novos Clientes</span><span class="kpi-icon">👤</span></div><div class="kpi-value"><?php echo $customerAnalytics['new_customers']; ?></div><div class="kpi-detail">no período</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Clientes Ativos</span><span class="kpi-icon">🏃</span></div><div class="kpi-value"><?php echo $customerAnalytics['active_customers']; ?></div><div class="kpi-detail">compraram no período</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Taxa de Retenção</span><span class="kpi-icon">🔄</span></div><div class="kpi-value"><?php echo number_format($customerAnalytics['retention_rate'] ?? 0, 1); ?>%</div><div class="kpi-detail">clientes recorrentes</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">LTV Médio</span><span class="kpi-icon">💰</span></div><div class="kpi-value">R$ <?php echo number_format($customerAnalytics['avg_ltv'] ?? 0, 2, ',', '.'); ?></div><div class="kpi-detail">lifetime value</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Taxa de Conversão</span><span class="kpi-icon">🛒</span></div><div class="kpi-value"><?php echo number_format($conversionRate, 1); ?>%</div><div class="kpi-detail">carrinho → pedido</div></div>
    </div>

    <div class="dashboard-card chart-card">
        <h3>📊 Vendas por Dia</h3>
        <div class="chart-wrapper">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>📂 Vendas por Categoria</h3>
            <div class="chart-wrapper-small">
                <canvas id="categorySalesChart"></canvas>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead><tr><th>Categoria</th><th>Pedidos</th><th>Itens</th><th>Receita</th></tr></thead>
                    <tbody><?php foreach($salesByCategory as $category): ?><tr><td><?php echo $category['category']; ?></td><td><?php echo $category['orders']; ?></td><td><?php echo $category['items_sold']; ?></td><td>R$ <?php echo number_format($category['revenue'], 2, ',', '.'); ?></td></tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
        <div class="dashboard-card">
            <h3>💳 Vendas por Método de Pagamento</h3>
            <div class="chart-wrapper-small">
                <canvas id="paymentMethodChart"></canvas>
            </div>
            <div class="table-container">
                 <table class="data-table">
                    <thead><tr><th>Método</th><th>Pedidos</th><th>Receita</th><th>% Total</th></tr></thead>
                    <tbody>
                        <?php $totalRevenue = array_sum(array_column($salesByPaymentMethod, 'revenue'));
                        foreach($salesByPaymentMethod as $payment): 
                            $percentage = $totalRevenue > 0 ? ($payment['revenue'] / $totalRevenue) * 100 : 0;?>
                            <tr>
                                <td><?php echo ucfirst($payment['payment_method']); ?></td>
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
</div>

<style>
/* Importa estilos do Dashboard para consistência */
.admin-container { max-width: 1600px; margin: 0 auto; padding: 2rem; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.kpi-card { background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%); padding: 1.5rem; border-radius: var(--border-radius-xl); border: 1px solid rgba(139, 92, 246, 0.2); }
.kpi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.kpi-title { color: var(--text-secondary); font-size: 0.875rem; font-weight: 500; }
.kpi-icon { font-size: 1.5rem; }
.kpi-value { font-size: 1.8rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.5rem; }
.kpi-detail { color: var(--text-secondary); font-size: 0.875rem; }
.dashboard-card { background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%); padding: 1.5rem; border-radius: var(--border-radius-xl); border: 1px solid rgba(139, 92, 246, 0.2); margin-bottom: 2rem; }
.dashboard-card h3 { color: var(--primary-color); margin-bottom: 1.5rem; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
.chart-wrapper { position: relative; height: 300px; width: 100%; }
.chart-wrapper-small { position: relative; height: 200px; width: 100%; margin-bottom: 1rem; }
.table-container { max-height: 300px; overflow-y: auto; }
.data-table { width: 100%; font-size: 0.875rem; border-collapse: collapse; }
.data-table th, .data-table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border-color); }
.data-table th { color: var(--primary-color); }
/* Filtros */
.filters-section { padding: 1.5rem; }
.period-filter { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
.filter-group { display: flex; flex-direction: column; gap: 0.5rem; }
@media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dados para os gráficos
const salesByDayData = <?php echo json_encode($salesByDay); ?>;
const salesByCategoryData = <?php echo json_encode($salesByCategory); ?>;
const salesByPaymentData = <?php echo json_encode($salesByPaymentMethod); ?>;

// Gráfico de Vendas por Dia
new Chart(document.getElementById('dailySalesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: salesByDayData.map(item => new Date(item.date).toLocaleDateString('pt-BR')),
        datasets: [{
            label: 'Receita Diária',
            data: salesByDayData.map(item => parseFloat(item.revenue)),
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
});

// Gráfico de Vendas por Categoria
new Chart(document.getElementById('categorySalesChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: salesByCategoryData.map(item => item.category),
        datasets: [{
            data: salesByCategoryData.map(item => parseFloat(item.revenue)),
            backgroundColor: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

// Gráfico de Métodos de Pagamento
new Chart(document.getElementById('paymentMethodChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: salesByPaymentData.map(item => item.payment_method),
        datasets: [{
            data: salesByPaymentData.map(item => parseFloat(item.revenue)),
            backgroundColor: ['#22c55e', '#3b82f6', '#fbbf24']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include '../views/layout/footer.php'; ?>