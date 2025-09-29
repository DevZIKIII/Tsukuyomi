<?php include '../views/layout/header.php'; ?>

<div class="form-container" style="max-width: 800px;">
    <h2>Crie Sua Conta na Tsukuyomi</h2>
    <p style="text-align: center; color: var(--text-secondary); margin-top: -1rem; margin-bottom: 2rem;">Junte-se à nossa comunidade e vista seu poder!</p>
    
    <form action="index.php?action=store_user" method="POST">
        
        <h4>Dados da Conta</h4>
        <div class="form-group">
            <label for="name">👤 Nome Completo *</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">📧 Email *</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex: 1;">
                <label for="password">🔒 Senha *</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small>Mínimo de 6 caracteres</small>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="confirm_password">✅ Confirmar Senha *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
        </div>

        <hr style="border-color: var(--border-color); margin: 2rem 0;">

        <h4>Endereço de Entrega</h4>
        <div class="form-group">
            <label for="phone">📱 Telefone</label>
            <input type="tel" id="phone" name="phone" class="form-control" placeholder="(11) 99999-9999">
        </div>
        <div class="form-group">
            <label for="zip_code">📮 CEP</label>
            <input type="text" id="zip_code" name="zip_code" class="form-control" placeholder="00000-000">
        </div>
        <div class="form-group">
            <label for="address">🏠 Endereço (Rua/Avenida)</label>
            <input type="text" id="address" name="address" class="form-control">
        </div>
        <div class="form-row">
            <div class="form-group" style="flex: 1;">
                <label for="number">Nº *</label>
                <input type="text" id="number" name="number" class="form-control" required>
            </div>
            <div class="form-group" style="flex: 2;">
                <label for="complement">Complemento</label>
                <input type="text" id="complement" name="complement" class="form-control" placeholder="Apto, bloco, etc.">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="city">🏙️ Cidade</label>
                <input type="text" id="city" name="city" class="form-control">
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="state">🗺️ Estado</label>
                <select id="state" name="state" class="form-control">
                    <option value="">UF</option>
                    <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option><option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option><option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option><option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option><option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option><option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                </select>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">Criar Conta</button>
        
        <p style="margin-top: 1rem; text-align: center;">
            Já tem uma conta? <a href="index.php?action=login">Faça login</a>
        </p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica ViaCEP
    const zipCodeInput = document.getElementById('zip_code');
    const addressInput = document.getElementById('address');
    const numberInput = document.getElementById('number');
    const cityInput = document.getElementById('city');
    const stateSelect = document.getElementById('state');

    if (zipCodeInput) {
        zipCodeInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            addressInput.value = 'Buscando...';
            cityInput.value = 'Buscando...';

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        addressInput.value = data.logradouro;
                        cityInput.value = data.localidade;
                        stateSelect.value = data.uf;
                        numberInput.focus();
                    } else {
                        alert('CEP não encontrado.');
                        addressInput.value = '';
                        cityInput.value = '';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    alert('Ocorreu um erro ao buscar o CEP.');
                });
        });
    }

    // Máscaras
    const phoneInput = document.getElementById('phone');
    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 8);
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    }
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            }
            e.target.value = value;
        });
    }
});
</script>

<style>
    .form-row {
        display: flex;
        gap: 1rem;
    }
    .form-container h4 {
        text-align: left;
        width: 100%;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }
</style>

<?php include '../views/layout/footer.php'; ?>