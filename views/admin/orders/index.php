<?php include '../../layout/header.php'; ?>

<div class="admin-container">
    <h1>Todos os Pedidos</h1>
    
    <div class="table-container">
        <table class="data-table">
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
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                        <td>
                            <?php 
                            $payment_labels = [
                                'card' => 'Cartão',
                                'credit_card' => 'Cartão de Crédito',
                                'debit_card' => 'Cartão de Débito',
                                'pix' => 'PIX',
                                'boleto' => 'Boleto'
                            ];
                            echo $payment_labels[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </td>
                        <td>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php
                                $status_labels = [
                                    'pending' => 'Pendente',
                                    'processing' => 'Processando',
                                    'shipped' => 'Enviado',
                                    'delivered' => 'Entregue',
                                    'cancelled' => 'Cancelado'
                                ];
                                echo $status_labels[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="index.php?action=order&id=<?php echo $order['id']; ?>" 
                               class="btn btn-sm btn-primary">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(empty($orders)): ?>
        <p class="text-center">Nenhum pedido encontrado.</p>
    <?php endif; ?>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
}

.table-container {
    overflow-x: auto;
    margin: 2rem 0;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--surface-color);
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    background-color: var(--surface-color);
    font-weight: 600;
    color: var(--primary-color);
}

.data-table tr:hover {
    background-color: rgba(139, 92, 246, 0.05);
}

.order-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-pending { 
    background-color: #fbbf24; 
    color: #78350f; 
}

.status-processing { 
    background-color: #60a5fa; 
    color: #1e3a8a; 
}

.status-shipped { 
    background-color: #a78bfa; 
    color: #2e1065; 
}

.status-delivered { 
    background-color: #34d399; 
    color: #064e3b; 
}

.status-cancelled { 
    background-color: #f87171; 
    color: #7f1d1d; 
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .data-table {
        font-size: 0.875rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.5rem;
    }
}
</style>

<?php include '../../layout/footer.php'; ?>