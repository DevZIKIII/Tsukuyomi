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
    
    // Converter código para maiúsculo automaticamente
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
    
    // Alterar campos baseado no tipo de desconto
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
    
    // Definir data/hora atual como padrão para "válido a partir de"
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    validFrom.value = now.toISOString().slice(0, 16);
    
    // Definir data/hora uma semana à frente como padrão para "válido até"
    const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    validUntil.value = nextWeek.toISOString().slice(0, 16);
    
    // Validação de datas
    validFrom.addEventListener('change', function() {
        validUntil.min = this.value;
    });
    
    validUntil.addEventListener('change', function() {
        if (this.value <= validFrom.value) {
            alert('A data de término deve ser posterior à data de início');
            this.value = '';
        }
    });
});
</script>

<style>
.form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-right: 10px;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

h2 {
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}
</style>

<?php include '../views/layout/footer.php'; ?>