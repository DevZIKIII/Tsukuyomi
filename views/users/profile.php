<?php 
if (file_exists('../views/layout/header.php')) {
    include '../views/layout/header.php';
} elseif (file_exists('../../views/layout/header.php')) {
    include '../../views/layout/header.php';
} else {
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/header.php';
}
?>

<div class="profile-container">
    <div class="profile-header">
        <h2>Meu Perfil</h2>
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

    <div class="profile-content">
        <div class="profile-form">
            <form action="/tsukuyomi/public/index.php?action=update_profile" method="POST">
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
                        <?php 
                        $states = [
                            'AC' => 'Acre',
                            'AL' => 'Alagoas',
                            'AP' => 'Amapá',
                            'AM' => 'Amazonas',
                            'BA' => 'Bahia',
                            'CE' => 'Ceará',
                            'DF' => 'Distrito Federal',
                            'ES' => 'Espírito Santo',
                            'GO' => 'Goiás',
                            'MA' => 'Maranhão',
                            'MT' => 'Mato Grosso',
                            'MS' => 'Mato Grosso do Sul',
                            'MG' => 'Minas Gerais',
                            'PA' => 'Pará',
                            'PB' => 'Paraíba',
                            'PR' => 'Paraná',
                            'PE' => 'Pernambuco',
                            'PI' => 'Piauí',
                            'RJ' => 'Rio de Janeiro',
                            'RN' => 'Rio Grande do Norte',
                            'RS' => 'Rio Grande do Sul',
                            'RO' => 'Rondônia',
                            'RR' => 'Roraima',
                            'SC' => 'Santa Catarina',
                            'SP' => 'São Paulo',
                            'SE' => 'Sergipe',
                            'TO' => 'Tocantins'
                        ];
                        
                        foreach($states as $code => $name): 
                        ?>
                            <option value="<?php echo $code; ?>" 
                                    <?php echo (isset($user->state) && $user->state == $code) ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="zip_code">CEP</label>
                    <input type="text" id="zip_code" name="zip_code" class="form-control" 
                           value="<?php echo htmlspecialchars($user->zip_code ?? ''); ?>"
                           placeholder="00000-000">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                    <a href="/tsukuyomi/public/index.php?action=orders" class="btn btn-secondary">Meus Pedidos</a>
                </div>
            </form>
        </div>
        
        <div class="profile-info">
            <div class="info-card">
                <h3>Informações da Conta</h3>
                <p><strong>Membro desde:</strong> 
                   <?php echo isset($user->created_at) ? date('d/m/Y', strtotime($user->created_at)) : 'N/A'; ?>
                </p>
                <p><strong>Tipo de conta:</strong> 
                   <?php echo isset($user->user_type) ? ucfirst($user->user_type) : 'Cliente'; ?>
                </p>
            </div>
            
            <div class="info-card">
                <h3>Alterar Senha</h3>
                <form action="/tsukuyomi/public/index.php?action=change_password" method="POST">
                    <div class="form-group">
                        <label for="current_password">Senha Atual</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Nova Senha</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Alterar Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const zipCode = document.getElementById('zip_code');
    const phone = document.getElementById('phone');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Máscara para CEP
    if (zipCode) {
        zipCode.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            this.value = value;
        });
    }
    
    // Máscara para telefone
    if (phone) {
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
    }
    
    // Validação de senha
    if (confirmPassword) {
        confirmPassword.addEventListener('blur', function() {
            if (newPassword.value !== '' && this.value !== newPassword.value) {
                alert('As senhas não coincidem!');
                this.focus();
            }
        });
    }
    
    // Validação do formulário de senha
    const passwordForm = document.querySelector('form[action*="change_password"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                confirmPassword.focus();
            }
        });
    }
});
</script>

<style>
.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
} */

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c2c7;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<?php 
// Include footer com fallbacks
if (file_exists('../views/layout/footer.php')) {
    include '../views/layout/footer.php';
} elseif (file_exists('../../views/layout/footer.php')) {
    include '../../views/layout/footer.php';
} else {
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/footer.php';
}
?>