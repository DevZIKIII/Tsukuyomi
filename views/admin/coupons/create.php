<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Adicionar Novo Cupom</h2>
    
    <form action="/tsukuyomi/public/index.php?action=store_coupon" method="POST">
        <div class="form-group">
            <label for="code">Código do Cupom *</label>
            <input type="text" id="code" name="code" class="form-control" 
                   placeholder="Ex: DESCONTO10, PROMOCAO2025" 
                   style="text-transform: uppercase;" required>
            <small>Use apenas letras, números e sem espaços</small>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição *</label>
            <input type="text" id="description" name="description" class="form-control" 
                   placeholder="Ex: Desconto de 10% em toda loja" required>
        </div>
        
        <div class="form-group">
            <label for="discount_type">Tipo de Desconto *</label>
            <select id="discount_type" name="discount_type" class="form-control" required>
                <option value="">Selecione o tipo</option>
                <option value="percentage">Porcentagem (%)</option>
                <option value="fixed">Valor Fixo (R$)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="discount_value">Valor do Desconto *</label>
            <input type="number" id="discount_value" name="discount_value" class="form-control" 
                   step="0.01" min="0" required>
            <small id="discount_help">Para porcentagem: insira apenas o número (ex: 10 para 10%)</small>
        </div>
        
        <div class="form-group">
            <label for="min_order_value">Valor Mínimo do Pedido (R$)</label>
            <input type="number" id="min_order_value" name="min_order_value" class="form-control" 
                   step="0.01" min="0" value="0">
            <small>Deixe 0 se não houver valor mínimo</small>
        </div>
        
        <div class="form-group" id="max_discount_group" style="display: none;">
            <label for="max_discount">Desconto Máximo (R$)</label>
            <input type="number" id="max_discount" name="max_discount" class="form-control" 
                   step="0.01" min="0">
            <small>Apenas para cupons de porcentagem - limite máximo em reais</small>
        </div>
        
        <div class="form-group">
            <label for="usage_limit">Limite de Uso</label>
            <input type="number" id="usage_limit" name="usage_limit" class="form-control" 
                   min="1" placeholder="Deixe vazio para uso ilimitado">
            <small>Número máximo de vezes que o cupom pode ser usado</small>
        </div>
        
        <div class="form-group">
            <label for="valid_from">Válido A Partir De *</label>
            <input type="datetime-local" id="valid_from" name="valid_from" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="valid_until">Válido Até *</label>
            <input type="datetime-local" id="valid_until" name="valid_until" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Criar Cupom</button>
        <a href="/tsukuyomi/public/index.php?action=coupons" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const discountHelp = document.getElementById('discount_help');
    const maxDiscountGroup = document.getElementById('max_discount_group');
    const codeInput = document.getElementById('code');
    const validFrom = document.getElementById('valid_from');
    const validUntil = document.getElementById('valid_until');
    
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
    
    discountType.addEventListener('change', function() {
        if (this.value === 'percentage') {
            discountValue.setAttribute('max', '100');
            discountHelp.textContent = 'Insira a porcentagem (ex: 10 para 10%)';
            maxDiscountGroup.style.display = 'block';
        } else if (this.value === 'fixed') {
            discountValue.removeAttribute('max');
            discountHelp.textContent = 'Insira o valor em reais (ex: 25.00)';
            maxDiscountGroup.style.display = 'none';
        }
    });
    
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    validFrom.value = now.toISOString().slice(0, 16);
    
    const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    validUntil.value = nextWeek.toISOString().slice(0, 16);
    
    validFrom.addEventListener('change', function() {
        validUntil.min = this.value;
    });
    
    validUntil.addEventListener('change', function() {
        if (this.value <= validFrom.value) {
            Swal.fire({ icon: 'warning', title: 'Data Inválida', text: 'A data de término deve ser posterior à data de início.', confirmButtonColor: 'var(--primary-color)' });
            this.value = '';
        }
    });
});
</script>

<?php include '../views/layout/footer.php'; ?>