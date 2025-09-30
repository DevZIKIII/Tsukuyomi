<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📋 Detalhes do Pedido #<?php echo $orderDetails['id']; ?></h1>
        <a href="index.php?action=orders" class="btn btn-secondary">
            ← Voltar aos Pedidos
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

    <div class="info-grid">
        <div class="info-card">
            <h3>📦 Informações do Pedido</h3>
            <div class="info-item">
                <span class="info-label">Número do Pedido:</span>
                <span class="info-value"><strong>#<?php echo $orderDetails['id']; ?></strong></span>
            </div>
            <div class="info-item">
                <span class="info-label">Data do Pedido:</span>
                <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($orderDetails['created_at'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <?php 
                    $status_class = '';
                    $status_text = '';
                    switch($orderDetails['status']) {
                        case 'pending': $status_class = 'badge-warning'; $status_text = 'Pendente'; break;
                        case 'processing': $status_class = 'badge-info'; $status_text = 'Processando'; break;
                        case 'shipped': $status_class = 'badge-primary'; $status_text = 'Enviado'; break;
                        case 'delivered': $status_class = 'badge-success'; $status_text = 'Entregue'; break;
                        case 'cancelled': $status_class = 'badge-danger'; $status_text = 'Cancelado'; break;
                        default: $status_class = 'badge-secondary'; $status_text = $orderDetails['status'];
                    }
                    ?>
                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Valor Total:</span>
                <span class="info-value info-highlight">R$ <?php echo number_format($orderDetails['total_amount'], 2, ',', '.'); ?></span>
            </div>
        </div>

        <div class="info-card">
            <h3>👤 Dados do Cliente</h3>
            <div class="info-item">
                <span class="info-label">Nome:</span>
                <span class="info-value"><?php echo htmlspecialchars($orderDetails['user_name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($orderDetails['user_email']); ?></span>
            </div>
        </div>

        <div class="info-card">
            <h3>💳 Pagamento e Entrega</h3>
            <div class="info-item">
                <span class="info-label">Forma de Pagamento:</span>
                <span class="info-value">
                    <?php 
                    $payment_labels = [ 'credit_card' => 'Cartão de Crédito', 'debit_card' => 'Cartão de Débito', 'pix' => 'PIX', 'boleto' => 'Boleto', 'card' => 'Cartão', 'bank_slip' => 'Boleto' ];
                    echo $payment_labels[$orderDetails['payment_method']] ?? ucfirst($orderDetails['payment_method']);
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Endereço de Entrega:</span>
                <span class="info-value"><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></span>
            </div>
        </div>
    </div>

    <div class="section-container">
        <h2>🛍️ Itens do Pedido</h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Tamanho</th>
                        <th>Quantidade</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($order_items as $item): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="images/products/<?php echo $item['image_url']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="product-thumb"
                                         onerror="this.src='images/placeholder.jpg'">
                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo $item['size']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td><strong>R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Total do Pedido:</strong></td>
                        <td class="total-value">
                            <strong>R$ <?php echo number_format($orderDetails['total_amount'], 2, ',', '.'); ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
        <div class="section-container">
            <h2>⚙️ Ações do Administrador</h2>
            <div class="admin-actions-card">
                <form method="POST" action="index.php?action=update_order_status" class="status-update-form">
                    <input type="hidden" name="order_id" value="<?php echo $orderDetails['id']; ?>">
                    <div class="form-group">
                        <label for="status">Atualizar Status do Pedido:</label>
                        <div class="status-update-group">
                            <select name="status" id="status" class="form-control">
                                <option value="pending" <?php echo $orderDetails['status'] == 'pending' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="processing" <?php echo $orderDetails['status'] == 'processing' ? 'selected' : ''; ?>>Processando</option>
                                <option value="shipped" <?php echo $orderDetails['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                <option value="delivered" <?php echo $orderDetails['status'] == 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                                <option value="cancelled" <?php echo $orderDetails['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Atualizar Status</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>