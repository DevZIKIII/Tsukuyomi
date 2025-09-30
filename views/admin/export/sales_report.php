<?php
// views/admin/export/sales_report.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📈 Relatório de Vendas Detalhado</h1>
        <div class="header-actions">
            <a href="index.php?action=export" class="btn btn-secondary">
                ← Voltar
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Imprimir
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_orders']); ?></h3>
                <p>Total de Pedidos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_customers']); ?></h3>
                <p>Clientes Únicos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3>R$ <?php echo number_format($stats['total_revenue'], 2, ',', '.'); ?></h3>
                <p>Receita Total</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🧮</div>
            <div class="stat-info">
                <h3>R$ <?php echo number_format($stats['avg_order_value'], 2, ',', '.'); ?></h3>
                <p>Ticket Médio</p>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <h2>📊 Vendas por Período</h2>
        <canvas id="salesChart"></canvas>
    </div>

    <div class="report-section">
        <h2>🏆 Top 10 Produtos Mais Vendidos</h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço Unit.</th>
                        <th>Qtd. Vendida</th>
                        <th>Receita Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $position = 1; ?>
                    <?php foreach($productSales as $product): ?>
                        <tr>
                            <td>
                                <span class="position-badge"><?php echo $position++; ?>º</span>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>
                                <span class="category-badge"><?php echo $product['category']; ?></span>
                            </td>
                            <td>R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                            <td>
                                <strong><?php echo $product['total_quantity'] ?: 0; ?></strong>
                            </td>
                            <td class="revenue-highlight">
                                R$ <?php echo number_format($product['total_revenue'] ?: 0, 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-section">
        <h2>📂 Vendas por Categoria</h2>
        <div class="categories-grid">
            <?php foreach($categorySales as $category): ?>
                <div class="category-card">
                    <h4><?php echo $category['category']; ?></h4>
                    <div class="category-stats">
                        <div class="stat-item">
                            <span class="stat-label">Produtos:</span>
                            <span class="stat-value"><?php echo $category['total_products']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Vendidos:</span>
                            <span class="stat-value"><?php echo $category['total_sold'] ?: 0; ?></span>
                        </div>
                        <div class="stat-item highlight">
                            <span class="stat-label">Receita:</span>
                            <span class="stat-value">R$ <?php echo number_format($category['total_revenue'] ?: 0, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                    <?php 
                    $percentage = 0;
                    if($stats['total_revenue'] > 0) {
                        $percentage = ($category['total_revenue'] / $stats['total_revenue']) * 100;
                    }
                    ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                    <small><?php echo number_format($percentage, 1); ?>% do total</small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="report-section">
        <h2>⭐ Top 10 Melhores Clientes</h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Pedidos</th>
                        <th>Gasto Total</th>
                        <th>Último Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $position = 1; ?>
                    <?php foreach($topCustomers as $customer): ?>
                        <tr>
                            <td>
                                <span class="position-badge gold"><?php echo $position++; ?>º</span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($customer['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td>
                                <span class="badge"><?php echo $customer['total_orders']; ?></span>
                            </td>
                            <td class="revenue-highlight">
                                R$ <?php echo number_format($customer['total_spent'], 2, ',', '.'); ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($customer['last_order_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="export-actions-section">
        <h3>💾 Exportar este Relatório</h3>
        <div class="export-buttons">
            <a href="index.php?action=export_data&type=sales&period=all&format=csv" class="btn btn-secondary">
                📄 Exportar CSV
            </a>
            <a href="index.php?action=export_data&type=sales&period=all&format=json" class="btn btn-secondary">
                📋 Exportar JSON
            </a>
            <button onclick="exportToPDF()" class="btn btn-primary">
                📑 Exportar PDF
            </button>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
}

.stat-icon {
    font-size: 2.5rem;
}

.stat-info h3 {
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.stat-info p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.chart-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    margin-bottom: 3rem;
}

.report-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
}

.report-section h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.position-badge {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.875rem;
}

.position-badge.gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
}

.category-badge {
    background: rgba(139, 92, 246, 0.1);
    color: var(--primary-color);
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
}

.revenue-highlight {
    font-weight: bold;
    color: var(--success-color);
    font-size: 1.1rem;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: rgba(15, 15, 15, 0.5);
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    border: 1px solid rgba(139, 92, 246, 0.1);
}

.category-card h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.category-stats {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-item.highlight {
    font-weight: bold;
    color: var(--success-color);
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(139, 92, 246, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin: 0.5rem 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), #a78bfa);
    transition: width 0.5s ease;
}

.export-actions-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
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

@media print {
    .header-actions,
    .export-actions-section {
        display: none;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de vendas
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        datasets: [{
            label: 'Vendas 2025',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000, 40000, 38000, 45000],
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
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

function exportToPDF() {
    Swal.fire({
        icon: 'info',
        title: 'Em Breve',
        text: 'A funcionalidade de exportação para PDF será implementada em breve!',
        confirmButtonColor: 'var(--primary-color)'
    });
}
</script>

<?php include '../views/layout/footer.php'; ?>