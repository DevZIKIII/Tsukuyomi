<?php 
// SOLUÇÃO DEFINITIVA: Testamos vários caminhos possíveis
// Substitua as duas linhas abaixo pelos caminhos corretos da sua estrutura

// OPÇÃO 1: Se header.php está em views/layout/
if (file_exists('../../../views/layout/header.php')) {
    include '../../../views/layout/header.php';
} 
// OPÇÃO 2: Se header.php está em views/
elseif (file_exists('../../header.php')) {
    include '../../header.php';
}
// OPÇÃO 3: Se header.php está na pasta layout dentro de views/
elseif (file_exists('../../layout/header.php')) {
    include '../../layout/header.php';
}
// OPÇÃO 4: Se header.php está em uma pasta diferente
elseif (file_exists('../../../layout/header.php')) {
    include '../../../layout/header.php';
}
// OPÇÃO 5: Usando caminho absoluto
else {
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/header.php';
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h2>Todos os Pedidos</h2>
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total de Pedidos</h3>
                <p><?php echo count($orders); ?></p>
            </div>
            <div class="stat-card">
                <h3>Valor Total</h3>
                <p>R$ <?php 
                    $total = 0;
                    foreach($orders as $order) {
                        $total += $order['total_amount'];
                    }
                    echo number_format($total, 2, ',', '.');
                ?></p>
            </div>
        </div>
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

    <div class="orders-filter">
        <form method="GET" action="/tsukuyomi/public/index.php">
            <input type="hidden" name="action" value="all_orders">
            <div class="filter-group">
                <label for="status">Filtrar por Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pendente</option>
                    <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processando</option>
                    <option value="shipped" <?php echo (isset($_GET['status']) && $_GET['status'] == 'shipped') ? 'selected' : ''; ?>>Enviado</option>
                    <option value="delivered" <?php echo (isset($_GET['status']) && $_GET['status'] == 'delivered') ? 'selected' : ''; ?>>Entregue</option>
                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($orders)): ?>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></td>
                            <td>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                            <td>
                                <span class="payment-method">
                                    <?php 
                                    $methods = [
                                        'credit_card' => 'Cartão de Crédito',
                                        'debit_card' => 'Cartão de Débito',
                                        'pix' => 'PIX',
                                        'bank_slip' => 'Boleto'
                                    ];
                                    echo $methods[$order['payment_method']] ?? $order['payment_method'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="status status-<?php echo $order['status']; ?>">
                                    <?php 
                                    $statusLabels = [
                                        'pending' => 'Pendente',
                                        'processing' => 'Processando',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregue',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    echo $statusLabels[$order['status']] ?? $order['status'];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td class="actions">
                                <a href="/tsukuyomi/public/index.php?action=order&id=<?php echo $order['id']; ?>" 
                                   class="btn btn-sm btn-primary">Ver Detalhes</a>
                                
                                <?php if($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                                    <div class="status-update">
                                        <form method="POST" action="/tsukuyomi/public/index.php?action=update_order_status" 
                                              style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="status-select">
                                                <option value="">Alterar Status</option>
                                                <?php if($order['status'] == 'pending'): ?>
                                                    <option value="processing">Processar</option>
                                                    <option value="cancelled">Cancelar</option>
                                                <?php elseif($order['status'] == 'processing'): ?>
                                                    <option value="shipped">Enviar</option>
                                                    <option value="cancelled">Cancelar</option>
                                                <?php elseif($order['status'] == 'shipped'): ?>
                                                    <option value="delivered">Entregar</option>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.admin-header h2 {
    color: #333;
    margin: 0;
}

.admin-stats {
    display: flex;
    gap: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    min-width: 120px;
    border: 1px solid #dee2e6;
}

.stat-card h3 {
    font-size: 14px;
    color: #666;
    margin: 0 0 10px 0;
}

.stat-card p {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.orders-filter {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: bold;
    color: #333;
}

.filter-group select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.admin-table tr:hover {
    background: #f8f9fa;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #d1ecf1;
    color: #0c5460;
}

.status-shipped {
    background: #d4edda;
    color: #155724;
}

.status-delivered {
    background: #d1e7dd;
    color: #0f5132;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.payment-method {
    padding: 2px 6px;
    background: #e9ecef;
    border-radius: 4px;
    font-size: 12px;
}

.actions {
    white-space: nowrap;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    cursor: pointer;
    margin-right: 5px;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 11px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.status-select {
    padding: 4px;
    font-size: 11px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 5px;
}

.alert {
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c2c7;
}

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 40px;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .admin-stats {
        width: 100%;
        justify-content: space-between;
        margin-top: 15px;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .admin-table {
        min-width: 800px;
    }
}
</style>

<?php 
// SOLUÇÃO DEFINITIVA: Testamos vários caminhos possíveis para o footer
// Substitua a linha abaixo pelo caminho correto da sua estrutura

// OPÇÃO 1: Se footer.php está em views/layout/
if (file_exists('../../../views/layout/footer.php')) {
    include '../../../views/layout/footer.php';
} 
// OPÇÃO 2: Se footer.php está em views/
elseif (file_exists('../../footer.php')) {
    include '../../footer.php';
}
// OPÇÃO 3: Se footer.php está na pasta layout dentro de views/
elseif (file_exists('../../layout/footer.php')) {
    include '../../layout/footer.php';
}
// OPÇÃO 4: Se footer.php está em uma pasta diferente
elseif (file_exists('../../../layout/footer.php')) {
    include '../../../layout/footer.php';
}
// OPÇÃO 5: Usando caminho absoluto
else {
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/footer.php';
}
?>