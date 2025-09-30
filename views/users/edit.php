<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Editar Usuário</h2>
    
    <form action="/tsukuyomi/public/index.php?action=update_user&id=<?php echo $user->id; ?>" method="POST">
        <div class="form-group">
            <label for="name">Nome Completo *</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Telefone</label>
            <input type="text" id="phone" name="phone" class="form-control" 
                   value="<?php echo htmlspecialchars($user->phone ?? ''); ?>"
                   placeholder="(11) 99999-9999">
        </div>
        
        <div class="form-group">
            <label for="address">Endereço</label>
            <input type="text" id="address" name="address" class="form-control" 
                   value="<?php echo htmlspecialchars($user->address ?? ''); ?>"
                   placeholder="Rua, número, complemento">
        </div>
        
        <div class="form-group">
            <label for="city">Cidade</label>
            <input type="text" id="city" name="city" class="form-control" 
                   value="<?php echo htmlspecialchars($user->city ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="state">Estado</label>
            <select id="state" name="state" class="form-control">
                <option value="">Selecione o estado</option>
                <option value="AC" <?php echo ($user->state ?? '') == 'AC' ? 'selected' : ''; ?>>Acre</option>
                <option value="AL" <?php echo ($user->state ?? '') == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                <option value="AP" <?php echo ($user->state ?? '') == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                <option value="AM" <?php echo ($user->state ?? '') == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                <option value="BA" <?php echo ($user->state ?? '') == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                <option value="CE" <?php echo ($user->state ?? '') == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                <option value="DF" <?php echo ($user->state ?? '') == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                <option value="ES" <?php echo ($user->state ?? '') == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                <option value="GO" <?php echo ($user->state ?? '') == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                <option value="MA" <?php echo ($user->state ?? '') == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                <option value="MT" <?php echo ($user->state ?? '') == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                <option value="MS" <?php echo ($user->state ?? '') == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                <option value="MG" <?php echo ($user->state ?? '') == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                <option value="PA" <?php echo ($user->state ?? '') == 'PA' ? 'selected' : ''; ?>>Pará</option>
                <option value="PB" <?php echo ($user->state ?? '') == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                <option value="PR" <?php echo ($user->state ?? '') == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                <option value="PE" <?php echo ($user->state ?? '') == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                <option value="PI" <?php echo ($user->state ?? '') == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                <option value="RJ" <?php echo ($user->state ?? '') == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                <option value="RN" <?php echo ($user->state ?? '') == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                <option value="RS" <?php echo ($user->state ?? '') == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                <option value="RO" <?php echo ($user->state ?? '') == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                <option value="RR" <?php echo ($user->state ?? '') == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                <option value="SC" <?php echo ($user->state ?? '') == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                <option value="SP" <?php echo ($user->state ?? '') == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                <option value="SE" <?php echo ($user->state ?? '') == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                <option value="TO" <?php echo ($user->state ?? '') == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="zip_code">CEP</label>
            <input type="text" id="zip_code" name="zip_code" class="form-control" 
                   value="<?php echo htmlspecialchars($user->zip_code ?? ''); ?>"
                   placeholder="00000-000">
        </div>
        
        <div class="form-group">
            <label for="user_type">Tipo de Usuário *</label>
            <select id="user_type" name="user_type" class="form-control" required>
                <option value="customer" <?php echo ($user->user_type ?? '') == 'customer' ? 'selected' : ''; ?>>Cliente</option>
                <option value="admin" <?php echo ($user->user_type ?? '') == 'admin' ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="new_password">Nova Senha</label>
            <input type="password" id="new_password" name="new_password" class="form-control"
                   placeholder="Deixe vazio para manter a senha atual">
            <small>Deixe em branco se não quiser alterar a senha</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmar Nova Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                   placeholder="Confirme a nova senha">
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
        <a href="/tsukuyomi/public/index.php?action=users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const zipCode = document.getElementById('zip_code');
    const phone = document.getElementById('phone');
    
    // Máscara para CEP
    zipCode.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        }
        this.value = value;
    });
    
    // Máscara para telefone
    phone.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        } else if (value.length >= 2) {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
        }
        this.value = value;
    });
    
    // Validação de senha
    confirmPassword.addEventListener('blur', function() {
        if (newPassword.value !== '' && this.value !== newPassword.value) {
            Swal.fire({ icon: 'error', title: 'Erro de Validação', text: 'As senhas não coincidem!', confirmButtonColor: 'var(--primary-color)' });
            this.focus();
        }
    });
    
    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        if (newPassword.value !== '' && confirmPassword.value !== newPassword.value) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Erro de Validação', text: 'As senhas não coincidem!', confirmButtonColor: 'var(--primary-color)' });
            confirmPassword.focus();
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