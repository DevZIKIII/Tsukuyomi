<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Criar Conta</h2>
    
    <form action="/tsukuyomi/public/index.php?action=store_user" method="POST">
        <div class="form-group">
            <label for="name">Nome Completo *</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Senha *</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <small>Mínimo de 6 caracteres</small>
        </div>
        
        <div class="form-group">
            <label for="phone">Telefone</label>
            <input type="tel" id="phone" name="phone" class="form-control" 
                   placeholder="(11) 99999-9999">
        </div>
        
        <div class="form-group">
            <label for="address">Endereço</label>
            <input type="text" id="address" name="address" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="city">Cidade</label>
            <input type="text" id="city" name="city" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="state">Estado</label>
            <select id="state" name="state" class="form-control">
                <option value="">Selecione</option>
                <option value="AC">AC</option>
                <option value="AL">AL</option>
                <option value="AP">AP</option>
                <option value="AM">AM</option>
                <option value="BA">BA</option>
                <option value="CE">CE</option>
                <option value="DF">DF</option>
                <option value="ES">ES</option>
                <option value="GO">GO</option>
                <option value="MA">MA</option>
                <option value="MT">MT</option>
                <option value="MS">MS</option>
                <option value="MG">MG</option>
                <option value="PA">PA</option>
                <option value="PB">PB</option>
                <option value="PR">PR</option>
                <option value="PE">PE</option>
                <option value="PI">PI</option>
                <option value="RJ">RJ</option>
                <option value="RN">RN</option>
                <option value="RS">RS</option>
                <option value="RO">RO</option>
                <option value="RR">RR</option>
                <option value="SC">SC</option>
                <option value="SP">SP</option>
                <option value="SE">SE</option>
                <option value="TO">TO</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="zip_code">CEP</label>
            <input type="text" id="zip_code" name="zip_code" class="form-control" 
                   placeholder="00000-000">
        </div>
        
        <button type="submit" class="btn btn-primary">Criar Conta</button>
        
        <p style="margin-top: 1rem; text-align: center;">
            Já tem uma conta? <a href="/tsukuyomi/public/index.php?action=login">Faça login</a>
        </p>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>