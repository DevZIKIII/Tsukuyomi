<?php
// views/admin/export/index.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📊 Exportar Dados do Sistema</h1>
        <a href="index.php?action=sales_report" class="btn btn-primary">
            📈 Ver Relatório de Vendas
        </a>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="export-grid">
        <!-- Exportar Produtos -->
        <div class="export-card">
            <div class="export-icon">📦</div>
            <h3>Produtos</h3>
            <p>Exportar catálogo completo de produtos com informações de estoque e vendas.</p>
            <div class="export-actions">
                <a href="index.php?action=export_data&type=products&format=csv" class="btn btn-secondary btn-sm">
                    📄 CSV
                </a>
                <a href="index.php?action=export_data&type=products&format=json" class="btn btn-secondary btn-sm">
                    📋 JSON
                </a>
            </div>
        </div>

        <!-- Exportar Pedidos -->
        <div class="export-card">
            <div class="export-icon">🛒</div>
            <h3>Pedidos</h3>
            <p>Exportar todos os pedidos com filtros opcionais.</p>
            <button class="btn btn-primary btn-sm" onclick="showOrderFilters()">
                ⚙️ Configurar Filtros
            </button>
            <div id="order-filters" style="display: none; margin-top: 1rem;">
                <form method="GET" action="index.php">
                    <input type="hidden" name="action" value="export_data">
                    <input type="hidden" name="type" value="orders">
                    
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="pending">Pendente</option>
                            <option value="processing">Processando</option>
                            <option value="shipped">Enviado</option>
                            <option value="delivered">Entregue</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Data Inicial:</label>
                        <input type="date" name="date_from" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Data Final:</label>
                        <input type="date" name="date_to" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Formato:</label>
                        <select name="format" class="form-control">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Exportar</button>
                </form>
            </div>
        </div>

        <!-- Exportar Usuários -->
        <div class="export-card">
            <div class="export-icon">👥</div>
            <h3>Usuários</h3>
            <p>Exportar lista de usuários (clientes e administradores).</p>
            <div class="export-actions">
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle">
                        Selecionar Tipo
                    </button>
                    <div class="dropdown-menu">
                        <a href="index.php?action=export_data&type=users&format=csv">
                            Todos (CSV)
                        </a>
                        <a href="index.php?action=export_data&type=users&format=json">
                            Todos (JSON)
                        </a>
                        <a href="index.php?action=export_data&type=users&user_type=customer&format=csv">
                            Clientes (CSV)
                        </a>
                        <a href="index.php?action=export_data&type=users&user_type=admin&format=csv">
                            Admins (CSV)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exportar Vendas -->
        <div class="export-card">
            <div class="export-icon">💰</div>
            <h3>Relatório de Vendas</h3>
            <p>Exportar relatório consolidado de vendas por período.</p>
            <div class="export-actions">
                <select id="sales-period" class="form-control" style="margin-bottom: 0.5rem;">
                    <option value="day">Hoje</option>
                    <option value="week">Esta Semana</option>
                    <option value="month" selected>Este Mês</option>
                    <option value="year">Este Ano</option>
                    <option value="all">Todo Período</option>
                </select>
                <button onclick="exportSales('csv')" class="btn btn-secondary btn-sm">📄 CSV</button>
                <button onclick="exportSales('json')" class="btn btn-secondary btn-sm">📋 JSON</button>
            </div>
        </div>

        <!-- Exportar Cupons -->
        <div class="export-card">
            <div class="export-icon">🎫</div>
            <h3>Cupons</h3>
            <p>Exportar todos os cupons de desconto cadastrados.</p>
            <div class="export-actions">
                <a href="index.php?action=export_data&type=coupons&format=csv" class="btn btn-secondary btn-sm">
                    📄 CSV
                </a>
                <a href="index.php?action=export_data&type=coupons&format=json" class="btn btn-secondary btn-sm">
                    📋 JSON
                </a>
            </div>
        </div>

        <!-- Exportar Inventário -->
        <div class="export-card">
            <div class="export-icon">📊</div>
            <h3>Inventário</h3>
            <p>Relatório completo de estoque e valor do inventário.</p>
            <div class="export-actions">
                <a href="index.php?action=export_data&type=inventory&format=csv" class="btn btn-secondary btn-sm">
                    📄 CSV
                </a>
                <a href="index.php?action=export_data&type=inventory&format=json" class="btn btn-secondary btn-sm">
                    📋 JSON
                </a>
            </div>
        </div>
    </div>

    <!-- Exportação Customizada -->
    <div class="custom-export-section">
        <h2>🔧 Exportação Customizada</h2>
        <p>Execute queries SQL personalizadas para exportar dados específicos (apenas SELECT permitido).</p>
        
        <form method="POST" action="index.php?action=export_data&type=custom">
            <div class="form-group">
                <label for="query">Query SQL:</label>
                <textarea id="query" name="query" class="form-control" rows="5" 
                          placeholder="SELECT * FROM products WHERE stock_quantity < 10"
                          required></textarea>
                <small>⚠️ Por segurança, apenas comandos SELECT são permitidos.</small>
            </div>
            
            <div class="form-group">
                <label>Formato de Exportação:</label>
                <select name="format" class="form-control">
                    <option value="csv">CSV</option>
                    <option value="json">JSON</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                🚀 Executar e Exportar
            </button>
        </form>
        
        <!-- Exemplos de Queries -->
        <div class="query-examples">
            <h4>📝 Exemplos de Queries:</h4>
            <ul>
                <li><code>SELECT * FROM products WHERE stock_quantity < 10</code> - Produtos com estoque baixo</li>
                <li><code>SELECT * FROM orders WHERE created_at >= '2025-01-01'</code> - Pedidos do ano atual</li>
                <li><code>SELECT u.name, COUNT(o.id) as total FROM users u JOIN orders o ON u.id = o.user_id GROUP BY u.id</code> - Total de pedidos por cliente</li>
            </ul>
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.export-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.export-card {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
    text-align: center;
    transition: all 0.3s ease;
}

.export-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
}

.export-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.export-card h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.export-card p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.export-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: none;
    position: absolute;
    background-color: var(--surface-color);
    min-width: 160px;
    box-shadow: var(--shadow-lg);
    z-index: 1;
    border-radius: var(--border-radius-md);
    border: 1px solid rgba(139, 92, 246, 0.2);
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-menu a {
    color: var(--text-primary);
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background 0.3s;
}

.dropdown-menu a:hover {
    background-color: rgba(139, 92, 246, 0.1);
}

.custom-export-section {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: var(--shadow-lg);
}

.custom-export-section h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.query-examples {
    margin-top: 2rem;
    padding: 1rem;
    background: rgba(15, 15, 15, 0.5);
    border-radius: var(--border-radius-md);
}

.query-examples h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.query-examples ul {
    list-style: none;
}

.query-examples li {
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: rgba(139, 92, 246, 0.05);
    border-radius: var(--border-radius-sm);
}

.query-examples code {
    color: var(--primary-color);
    font-family: monospace;
}

@media (max-width: 768px) {
    .export-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function showOrderFilters() {
    const filters = document.getElementById('order-filters');
    filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
}

function exportSales(format) {
    const period = document.getElementById('sales-period').value;
    window.location.href = `index.php?action=export_data&type=sales&period=${period}&format=${format}`;
}
</script>

<?php include '../views/layout/footer.php'; ?>