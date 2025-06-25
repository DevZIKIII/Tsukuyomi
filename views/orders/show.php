<?php include '../views/layout/header.php'; ?>

<div class="order-detail-container">
    <h1>Detalhes do Pedido #<?php echo $order['id']; ?></h1>
    
    <!-- Order Information -->
    <div class="order-detail-header">
        <div class="order-info-section">
            <h3>Informações do Pedido</h3>
            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> 
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
            </p>
            <p><strong>Total:</strong> R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></p>
        </div>
        
        <div class="order-info-section">
            <h3>Dados do Cliente</h3>
            <p><strong>Nome:</strong> <?php echo $order['user_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['user_email']; ?></p>
        </div>
        
        <div class="order-info-section">
            <h3>Pagamento e Entrega</h3>
            <p><strong>Forma de Pagamento:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
            <p><strong>Endereço de Entrega:</strong><br>
                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
            </p>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="order-items-section">
        <h2>Itens do Pedido</h2>
        
        <?php foreach($order_items as $item): ?>
            <div class="order-item">
                <img src="<?php echo BASE_URL; ?>images/products/<?php echo $item['image_url']; ?>"
                    alt="<?php echo $item['name']; ?>"
                    class="order-item-image"
                    onerror="this.src='<?php echo BASE_URL; ?>images/placeholder.jpg'">
                
                <div class="order-item-info">
                    <h4><?php echo $item['name']; ?></h4>
                    <p>Tamanho: <?php echo $item['size']; ?></p>
                    <p>Quantidade: <?php echo $item['quantity']; ?></p>
                </div>
                
                <div class="order-item-total">
                    <p>Preço unitário: R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                    <p><strong>Subtotal: R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></strong></p>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="order-summary">
            <h3>Resumo do Pedido</h3>
            <div class="order-total">
                <span>Total:</span>
                <span>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>
    
    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
        <div class="admin-actions">
            <h3>Ações do Administrador</h3>
            <form method="POST" action="/tsukuyomi/public/index.php?action=update_order_status">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <div class="form-group">
                    <label for="status">Atualizar Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processando</option>
                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Status</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="/tsukuyomi/public/index.php?action=orders" class="btn btn-secondary">Voltar aos Pedidos</a>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>