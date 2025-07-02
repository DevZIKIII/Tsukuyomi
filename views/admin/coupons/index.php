<?php 
// views/admin/coupons/index.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Gerenciar Cupons</h1>
        <a href="index.php?action=create_coupon" class="btn btn-primary">Criar Novo Cupom</a>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Pedido Mín.</th>
                    <th>Usos</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($coupons as $coupon): ?>
                    <tr>
                        <td><strong><?php echo $coupon['code']; ?></strong></td>
                        <td><?php echo $coupon['description']; ?></td>
                        <td>
                            <?php echo $coupon['discount_type'] == 'percentage' ? 'Porcentagem' : 'Valor Fixo'; ?>
                        </td>
                        <td>
                            <?php 
                            if($coupon['discount_type'] == 'percentage') {
                                echo $coupon['discount_value'] . '%';
                            } else {
                                echo 'R$ ' . number_format($coupon['discount_value'], 2, ',', '.');
                            }
                            ?>
                        </td>
                        <td>R$ <?php echo number_format($coupon['min_order_value'], 2, ',', '.'); ?></td>
                        <td>
                            <?php 
                            echo $coupon['used_count'];
                            if($coupon['usage_limit']) {
                                echo ' / ' . $coupon['usage_limit'];
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($coupon['valid_until'])); ?>
                        </td>
                        <td>
                            <?php if($coupon['is_active']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=toggle_coupon&id=<?php echo $coupon['id']; ?>" 
                               class="btn btn-sm btn-secondary">
                                <?php echo $coupon['is_active'] ? 'Desativar' : 'Ativar'; ?>
                            </a>
                            <a href="index.php?action=delete_coupon&id=<?php echo $coupon['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- <style>
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.badge-success {
    background-color: #22c55e;
    color: white;
}

.badge-danger {
    background-color: #ef4444;
    color: white;
}
</style> -->

<?php include '../views/layout/footer.php'; ?>

<?php 
// views/admin/coupons/create.php
?>
<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Criar Novo Cupom</h2>
    
    <form action="index.php?action=store_coupon" method="POST">
        <div class="form-group">
            <label for="code">Código do Cupom *</label>
            <input type="text" 
                   id="code" 
                   name="code" 
                   class="form-control" 
                   placeholder="Ex: DESCONTO10"
                   style="text-transform: uppercase;"
                   required>
            <small>O código será automaticamente convertido para maiúsculas</small>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição *</label>
            <input type="text" 
                   id="description" 
                   name="description" 
                   class="form-control" 
                   placeholder="Ex: 10% de desconto em toda a loja"
                   required>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="discount_type">Tipo de Desconto *</label>
                <select id="discount_type" name="discount_type" class="form-control" required onchange="updateDiscountLabel()">
                    <option value="percentage">Porcentagem (%)</option>
                    <option value="fixed">Valor Fixo (R$)</option>
                </select>
            </div>
            
            <div class="form-group half">
                <label for="discount_value">Valor do Desconto *</label>
                <input type="number" 
                       id="discount_value" 
                       name="discount_value" 
                       class="form-control" 
                       step="0.01"
                       placeholder="Ex: 10"
                       required>
                <small id="discount_help">Porcentagem de desconto (0-100)</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="min_order_value">Valor Mínimo do Pedido</label>
                <input type="number" 
                       id="min_order_value" 
                       name="min_order_value" 
                       class="form-control" 
                       step="0.01"
                       placeholder="0.00">
                <small>Deixe 0 para sem valor mínimo</small>
            </div>
            
            <div class="form-group half">
                <label for="max_discount">Desconto Máximo (R$)</label>
                <input type="number" 
                       id="max_discount" 
                       name="max_discount" 
                       class="form-control" 
                       step="0.01"
                       placeholder="Ex: 50.00">
                <small>Apenas para cupons de porcentagem</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="usage_limit">Limite de Uso</label>
            <input type="number" 
                   id="usage_limit" 
                   name="usage_limit" 
                   class="form-control" 
                   placeholder="Ex: 100">
            <small>Deixe vazio para uso ilimitado</small>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="valid_from">Válido de *</label>
                <input type="date" 
                       id="valid_from" 
                       name="valid_from" 
                       class="form-control" 
                       value="<?php echo date('Y-m-d'); ?>"
                       required>
            </div>
            
            <div class="form-group half">
                <label for="valid_until">Válido até *</label>
                <input type="date" 
                       id="valid_until" 
                       name="valid_until" 
                       class="form-control" 
                       value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"
                       required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Criar Cupom</button>
        <a href="index.php?action=coupons" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
function updateDiscountLabel() {
    const type = document.getElementById('discount_type').value;
    const help = document.getElementById('discount_help');
    const maxDiscountField = document.getElementById('max_discount').parentElement;
    
    if (type === 'percentage') {
        help.textContent = 'Porcentagem de desconto (0-100)';
        maxDiscountField.style.display = 'block';
    } else {
        help.textContent = 'Valor fixo de desconto em R$';
        maxDiscountField.style.display = 'none';
    }
}
</script>

<?php include '../views/layout/footer.php'; ?>