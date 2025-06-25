<?php include '../views/layout/header.php'; ?>

<h1>Meus Pedidos</h1>

<div class="orders-container">
    <?php if(count($orders) > 0): ?>
        <?php foreach($orders as $order): ?>
            <div class="order-card fade-in">
                <div class="order-header">
                    <div>
                        <h3>Pedido #<?php echo $order['id']; ?></h3>
                        <p class="order-date">
                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
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
                </div>
                
                <div class="order-body">
                    <p><strong>Total:</strong> R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></p>
                    <p><strong>Forma de Pagamento:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                </div>
                
                <div class="order-footer">
                    <a href="/tsukuyomi/public/index.php?action=order&id=<?php echo $order['id']; ?>" 
                       class="btn btn-primary btn-sm">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <p>Você ainda não fez nenhum pedido.</p>
            <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-primary mt-2">
                Começar a Comprar
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>