<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Todos os Pedidos</h1>
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

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pedido #</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($orders)): ?>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?></div>
                                <small><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></small>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><strong>R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                switch($order['status']) {
                                    case 'pending': $status_class = 'badge-warning'; $status_text = 'Pendente'; break;
                                    case 'processing': $status_class = 'badge-info'; $status_text = 'Processando'; break;
                                    case 'shipped': $status_class = 'badge-primary'; $status_text = 'Enviado'; break;
                                    case 'delivered': $status_class = 'badge-success'; $status_text = 'Entregue'; break;
                                    case 'cancelled': $status_class = 'badge-danger'; $status_text = 'Cancelado'; break;
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
                        <td colspan="6" class="text-center">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Estilos importados de /views/orders/show.php */
.admin-container{max-width:1200px;margin:2rem auto;padding:0 1rem}.admin-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}.admin-header h1{font-size:2rem;background:linear-gradient(135deg,var(--text-primary) 0%,var(--primary-color) 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;margin:0}.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:2rem;margin-bottom:3rem}.info-card{background:linear-gradient(135deg,var(--surface-color) 0%,rgba(139,92,246,.05) 100%);padding:2rem;border-radius:var(--border-radius-xl);border:1px solid rgba(139,92,246,.2);box-shadow:var(--shadow-lg)}.info-card h3{color:var(--primary-color);margin-bottom:1.5rem;font-size:1.25rem;position:relative;padding-bottom:.75rem}.info-card h3::after{content:'';position:absolute;bottom:0;left:0;width:50px;height:2px;background:var(--primary-color);border-radius:1px}.info-item{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;padding:.75rem 0;border-bottom:1px solid rgba(139,92,246,.1)}.info-item:last-child{border-bottom:none;margin-bottom:0}.info-label{color:var(--text-secondary);font-weight:500;flex-shrink:0}.info-value{color:var(--text-primary);text-align:right;flex:1;margin-left:1rem}.info-highlight{font-size:1.25rem;font-weight:700;color:var(--primary-color)}.section-container{background:linear-gradient(135deg,var(--surface-color) 0%,rgba(139,92,246,.05) 100%);padding:2rem;border-radius:var(--border-radius-xl);border:1px solid rgba(139,92,246,.2);box-shadow:var(--shadow-lg);margin-bottom:2rem}.section-container h2{color:var(--primary-color);margin-bottom:1.5rem;font-size:1.5rem}.table-container{overflow-x:auto}.data-table{width:100%;border-collapse:collapse}.data-table th,.data-table td{padding:1.5rem;text-align:left;border-bottom:1px solid rgba(139,92,246,.1)}.data-table th{background:linear-gradient(135deg,rgba(139,92,246,.1) 0%,rgba(139,92,246,.05) 100%);font-weight:700;color:var(--primary-color);text-transform:uppercase;letter-spacing:.05em;font-size:.875rem}.data-table td{color:var(--text-primary)}.data-table tfoot td{padding-top:1.5rem;font-size:1.1rem;border-top:2px solid rgba(139,92,246,.2)}.text-right{text-align:right}.total-value{color:var(--primary-color);font-size:1.25rem}.product-info{display:flex;align-items:center;gap:1rem}.product-thumb{width:50px;height:50px;object-fit:cover;border-radius:var(--border-radius-md)}.badge{display:inline-block;padding:.5rem 1rem;border-radius:50px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em}.badge-success{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;box-shadow:0 4px 12px rgba(34,197,94,.3)}.badge-warning{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#78350f;box-shadow:0 4px 12px rgba(251,191,36,.3)}.badge-info{background:linear-gradient(135deg,#60a5fa,#3b82f6);color:#fff;box-shadow:0 4px 12px rgba(96,165,250,.3)}.badge-primary{background:linear-gradient(135deg,#a78bfa,#8b5cf6);color:#fff;box-shadow:0 4px 12px rgba(167,139,250,.3)}.badge-danger{background:linear-gradient(135deg,#f87171,#ef4444);color:#fff;box-shadow:0 4px 12px rgba(248,113,113,.3)}.admin-actions-card{background:rgba(139,92,246,.05);padding:1.5rem;border-radius:var(--border-radius-lg);border:1px solid rgba(139,92,246,.2)}.status-update-form .form-group{margin:0}.status-update-form label{display:block;margin-bottom:1rem;color:var(--text-primary);font-weight:600}.status-update-group{display:flex;gap:1rem;align-items:center}.form-control{flex:1;padding:.875rem 1.25rem;background:rgba(15,15,15,.8);border:2px solid var(--border-color);border-radius:var(--border-radius-lg);color:var(--text-primary);font-size:1rem}.form-control:focus{outline:none;border-color:var(--primary-color);box-shadow:0 0 0 4px rgba(139,92,246,.1)}.alert{padding:1.5rem 2rem;border-radius:var(--border-radius-lg);margin-bottom:2rem;border:1px solid;position:relative;overflow:hidden}.alert::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:currentColor}.alert-success{background:rgba(34,197,94,.1);border-color:var(--success-color);color:var(--success-color)}.alert-error{background:rgba(239,68,68,.1);border-color:var(--error-color);color:var(--error-color)}@media (max-width:768px){.admin-header{flex-direction:column;align-items:flex-start}.info-grid{grid-template-columns:1fr;gap:1.5rem}.info-item{flex-direction:column;align-items:flex-start}.info-value{text-align:left;margin-left:0;margin-top:.5rem}.table-container{overflow-x:auto}.data-table{min-width:600px}.data-table th,.data-table td{padding:1rem;font-size:.875rem}.status-update-group{flex-direction:column}.form-control{width:100%}.product-info{flex-direction:column;align-items:flex-start;text-align:center}.product-thumb{width:80px;height:80px}}@media (max-width:480px){.admin-container{padding:0 .5rem}.section-container,.info-card{padding:1.5rem}.admin-header h1{font-size:1.5rem}.section-container h2{font-size:1.25rem}}
/* Estilos adicionais para admin/orders/index.php */
.admin-stats{display:flex;gap:1.5rem}.stat-card{background:linear-gradient(135deg,var(--surface-color) 0%,rgba(139,92,246,.05) 100%);padding:1.5rem;border-radius:var(--border-radius-lg);border:1px solid rgba(139,92,246,.2);min-width:150px;text-align:center}.stat-card h3{font-size:.875rem;color:var(--text-secondary);margin-bottom:.5rem;font-weight:500}.stat-card p{font-size:1.5rem;font-weight:700;color:var(--primary-color);margin:0}
</style>

<?php include '../views/layout/footer.php'; ?>