<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📦 Meus Pedidos</h1>
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

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pedido #</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($orders)): ?>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><strong>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <?php 
                                $payment_labels = [
                                    'credit_card' => 'Cartão de Crédito',
                                    'debit_card' => 'Cartão de Débito',
                                    'pix' => 'PIX',
                                    'boleto' => 'Boleto',
                                    'card' => 'Cartão',
                                    'bank_slip' => 'Boleto'
                                ];
                                echo $payment_labels[$order['payment_method']] ?? ucfirst($order['payment_method']);
                                ?>
                            </td>
                            <td>
                                <?php 
                                $status_class = '';
                                $status_text = '';
                                switch($order['status']) {
                                    case 'pending':
                                        $status_class = 'badge-warning';
                                        $status_text = 'Pendente';
                                        break;
                                    case 'processing':
                                        $status_class = 'badge-info';
                                        $status_text = 'Processando';
                                        break;
                                    case 'shipped':
                                        $status_class = 'badge-primary';
                                        $status_text = 'Enviado';
                                        break;
                                    case 'delivered':
                                        $status_class = 'badge-success';
                                        $status_text = 'Entregue';
                                        break;
                                    case 'cancelled':
                                        $status_class = 'badge-danger';
                                        $status_text = 'Cancelado';
                                        break;
                                    default:
                                        $status_class = 'badge-secondary';
                                        $status_text = $order['status'];
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?action=order&id=<?php echo $order['id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="empty-state">
                                <h3>📦 Você ainda não fez nenhum pedido</h3>
                                <p>Que tal começar a explorar nossa incrível coleção?</p>
                                <a href="index.php?action=products" class="btn btn-primary">
                                    🛍️ Começar a Comprar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Container principal */
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Header com estatísticas */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 2rem;
}

.admin-header h1 {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary-color) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

/* Cards de estatísticas */
.admin-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-card {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    border: 1px solid rgba(139, 92, 246, 0.2);
    min-width: 150px;
    text-align: center;
}

.stat-card h3 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-card p {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
}

/* Container da tabela */
.table-container {
    background: linear-gradient(135deg, var(--surface-color) 0%, rgba(139, 92, 246, 0.05) 100%);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(139, 92, 246, 0.2);
}

/* Tabela de dados */
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(139, 92, 246, 0.1);
}

.data-table th {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
    font-weight: 700;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.875rem;
}

.data-table tr:hover {
    background: rgba(139, 92, 246, 0.05);
}

.data-table td {
    color: var(--text-primary);
}

/* Badges de status */
.badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.badge-warning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #78350f;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.badge-info {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color: white;
    box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
}

.badge-primary {
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
}

.badge-danger {
    background: linear-gradient(135deg, #f87171, #ef4444);
    color: white;
    box-shadow: 0 4px 12px rgba(248, 113, 113, 0.3);
}

.badge-secondary {
    background: var(--border-color);
    color: var(--text-secondary);
}

/* Botões */
.btn-sm {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
}

/* Estado vazio */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

/* Alertas */
.alert {
    padding: 1.5rem 2rem;
    border-radius: var(--border-radius-lg);
    margin-bottom: 2rem;
    border: 1px solid;
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: currentColor;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    border-color: var(--success-color);
    color: var(--success-color);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border-color: var(--error-color);
    color: var(--error-color);
}

.text-center {
    text-align: center;
}

/* Responsivo */
@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .admin-stats {
        width: 100%;
        justify-content: space-between;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .data-table {
        min-width: 700px;
    }
    
    .data-table th,
    .data-table td {
        padding: 1rem;
        font-size: 0.875rem;
    }
}
</style>

<?php include '../views/layout/footer.php'; ?>