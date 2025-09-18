<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📋 Detalhes do Pedido #<?php echo $orderDetails['id']; ?></h1>
        <a href="index.php?action=all_orders" class="btn btn-secondary">
            ← Voltar
        </a>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
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
                    $payment_labels = ['card' => 'Cartão', 'pix' => 'PIX', 'boleto' => 'Boleto'];
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
                                         class="product-thumb">
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
                        <td colspan="4" class="text-right"><strong>Total do Pedido:</strong></td>
                        <td class="total-value">
                            <strong>R$ <?php echo number_format($orderDetails['total_amount'], 2, ',', '.'); ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

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
</div>

<style>
/* Estilos importados de /views/orders/show.php */
.admin-container{max-width:1200px;margin:2rem auto;padding:0 1rem}.admin-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}.admin-header h1{font-size:2rem;background:linear-gradient(135deg,var(--text-primary) 0%,var(--primary-color) 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;margin:0}.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:2rem;margin-bottom:3rem}.info-card{background:linear-gradient(135deg,var(--surface-color) 0%,rgba(139,92,246,.05) 100%);padding:2rem;border-radius:var(--border-radius-xl);border:1px solid rgba(139,92,246,.2);box-shadow:var(--shadow-lg)}.info-card h3{color:var(--primary-color);margin-bottom:1.5rem;font-size:1.25rem;position:relative;padding-bottom:.75rem}.info-card h3::after{content:'';position:absolute;bottom:0;left:0;width:50px;height:2px;background:var(--primary-color);border-radius:1px}.info-item{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;padding:.75rem 0;border-bottom:1px solid rgba(139,92,246,.1)}.info-item:last-child{border-bottom:none;margin-bottom:0}.info-label{color:var(--text-secondary);font-weight:500;flex-shrink:0}.info-value{color:var(--text-primary);text-align:right;flex:1;margin-left:1rem}.info-highlight{font-size:1.25rem;font-weight:700;color:var(--primary-color)}.section-container{background:linear-gradient(135deg,var(--surface-color) 0%,rgba(139,92,246,.05) 100%);padding:2rem;border-radius:var(--border-radius-xl);border:1px solid rgba(139,92,246,.2);box-shadow:var(--shadow-lg);margin-bottom:2rem}.section-container h2{color:var(--primary-color);margin-bottom:1.5rem;font-size:1.5rem}.table-container{overflow-x:auto}.data-table{width:100%;border-collapse:collapse}.data-table th,.data-table td{padding:1.5rem;text-align:left;border-bottom:1px solid rgba(139,92,246,.1)}.data-table th{background:linear-gradient(135deg,rgba(139,92,246,.1) 0%,rgba(139,92,246,.05) 100%);font-weight:700;color:var(--primary-color);text-transform:uppercase;letter-spacing:.05em;font-size:.875rem}.data-table td{color:var(--text-primary)}.data-table tfoot td{padding-top:1.5rem;font-size:1.1rem;border-top:2px solid rgba(139,92,246,.2)}.text-right{text-align:right}.total-value{color:var(--primary-color);font-size:1.25rem}.product-info{display:flex;align-items:center;gap:1rem}.product-thumb{width:50px;height:50px;object-fit:cover;border-radius:var(--border-radius-md)}.badge{display:inline-block;padding:.5rem 1rem;border-radius:50px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em}.badge-success{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 4px 12px rgba(34,197,94,.3)}.badge-warning{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#78350f;box-shadow:0 4px 12px rgba(251,191,36,.3)}.badge-info{background:linear-gradient(135deg,#60a5fa,#3b82f6);color:#fff;box-shadow:0 4px 12px rgba(96,165,250,.3)}.badge-primary{background:linear-gradient(135deg,#a78bfa,#8b5cf6);color:#fff;box-shadow:0 4px 12px rgba(167,139,250,.3)}.badge-danger{background:linear-gradient(135deg,#f87171,#ef4444);color:#fff;box-shadow:0 4px 12px rgba(248,113,113,.3)}.admin-actions-card{background:rgba(139,92,246,.05);padding:1.5rem;border-radius:var(--border-radius-lg);border:1px solid rgba(139,92,246,.2)}.status-update-form .form-group{margin:0}.status-update-form label{display:block;margin-bottom:1rem;color:var(--text-primary);font-weight:600}.status-update-group{display:flex;gap:1rem;align-items:center}.form-control{flex:1;padding:.875rem 1.25rem;background:rgba(15,15,15,.8);border:2px solid var(--border-color);border-radius:var(--border-radius-lg);color:var(--text-primary);font-size:1rem}.form-control:focus{outline:none;border-color:var(--primary-color);box-shadow:0 0 0 4px rgba(139,92,246,.1)}.alert{padding:1.5rem 2rem;border-radius:var(--border-radius-lg);margin-bottom:2rem;border:1px solid;position:relative;overflow:hidden}.alert::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:currentColor}.alert-success{background:rgba(34,197,94,.1);border-color:var(--success-color);color:var(--success-color)}.alert-error{background:rgba(239,68,68,.1);border-color:var(--error-color);color:var(--error-color)}@media (max-width:768px){.admin-header{flex-direction:column;align-items:flex-start}.info-grid{grid-template-columns:1fr;gap:1.5rem}.info-item{flex-direction:column;align-items:flex-start}.info-value{text-align:left;margin-left:0;margin-top:.5rem}.table-container{overflow-x:auto}.data-table{min-width:600px}.data-table th,.data-table td{padding:1rem;font-size:.875rem}.status-update-group{flex-direction:column}.form-control{width:100%}.product-info{flex-direction:column;align-items:flex-start;text-align:center}.product-thumb{width:80px;height:80px}}@media (max-width:480px){.admin-container{padding:0 .5rem}.section-container,.info-card{padding:1.5rem}.admin-header h1{font-size:1.5rem}.section-container h2{font-size:1.25rem}}
</style>

<?php include '../views/layout/footer.php'; ?>