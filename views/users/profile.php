<?php 
// Fallbacks para incluir o header
if (file_exists('../views/layout/header.php')) {
    include '../views/layout/header.php';
} elseif (file_exists('../../views/layout/header.php')) {
    include '../../views/layout/header.php';
} else {
    // Adapte este caminho se necessário
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/header.php';
}

// Lógica para separar o endereço salvo no banco nos campos do formulário
$address_parts = ['street' => '', 'number' => '', 'complement' => ''];
if (!empty($user->address)) {
    $parts = explode(',', $user->address);
    $address_parts['street'] = trim($parts[0] ?? '');
    // Verifica se a segunda parte é numérica para ser o número
    if (isset($parts[1]) && is_numeric(trim($parts[1]))) {
        $address_parts['number'] = trim($parts[1]);
        $address_parts['complement'] = trim($parts[2] ?? '');
    } else {
        // Se não for, pode ser parte do complemento
        $address_parts['complement'] = trim($parts[1] ?? '');
    }
}
?>

<div class="profile-container fade-in">
    <div class="profile-header">
        <h2>👤 Meu Perfil</h2>
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
            <form action="index.php?action=update_profile" method="POST">
                
                <div class="form-group">
                    <label for="name">👤 Nome Completo *</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">📧 Email *</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">📱 Telefone</label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($user->phone ?? ''); ?>"
                           placeholder="(11) 99999-9999">
                </div>

                <div class="form-group">
                    <label for="zip_code">📮 CEP</label>
                    <input type="text" id="zip_code" name="zip_code" class="form-control" 
                           value="<?php echo htmlspecialchars($user->zip_code ?? ''); ?>" placeholder="00000-000">
                </div>
                
                <div class="form-group">
                    <label for="address">🏠 Endereço (Rua/Avenida)</label>
                    <input type="text" id="address" name="address" class="form-control" 
                           value="<?php echo htmlspecialchars($address_parts['street']); ?>" placeholder="Ex: Rua João Corazza">
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="number">Nº *</label>
                        <input type="text" id="number" name="number" class="form-control" 
                               value="<?php echo htmlspecialchars($address_parts['number']); ?>" placeholder="Ex: 402" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label for="complement">Complemento</label>
                        <input type="text" id="complement" name="complement" class="form-control" 
                               value="<?php echo htmlspecialchars($address_parts['complement']); ?>" placeholder="Ex: Apto 101">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="city">🏙️ Cidade</label>
                        <input type="text" id="city" name="city" class="form-control" 
                               value="<?php echo htmlspecialchars($user->city ?? ''); ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="state">🗺️ Estado</label>
                        <select id="state" name="state" class="form-control">
                            <option value="">UF</option>
                            <?php 
                            $states = ['AC'=>'Acre', 'AL'=>'Alagoas', 'AP'=>'Amapá', 'AM'=>'Amazonas', 'BA'=>'Bahia', 'CE'=>'Ceará', 'DF'=>'Distrito Federal', 'ES'=>'Espírito Santo', 'GO'=>'Goiás', 'MA'=>'Maranhão', 'MT'=>'Mato Grosso', 'MS'=>'Mato Grosso do Sul', 'MG'=>'Minas Gerais', 'PA'=>'Pará', 'PB'=>'Paraíba', 'PR'=>'Paraná', 'PE'=>'Pernambuco', 'PI'=>'Piauí', 'RJ'=>'Rio de Janeiro', 'RN'=>'Rio Grande do Norte', 'RS'=>'Rio Grande do Sul', 'RO'=>'Rondônia', 'RR'=>'Roraima', 'SC'=>'Santa Catarina', 'SP'=>'São Paulo', 'SE'=>'Sergipe', 'TO'=>'Tocantins'];
                            foreach($states as $code => $name): ?>
                                <option value="<?php echo $code; ?>" <?php echo (isset($user->state) && $user->state == $code) ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Atualizar Perfil</button>
                    <a href="index.php?action=orders" class="btn btn-secondary">📦 Meus Pedidos</a>
                </div>
            </form>
        </div>
        
        <div class="profile-info">
            <div class="info-card">
                <h3>ℹ️ Informações da Conta</h3>
                <p><strong>📅 Membro desde:</strong> <?php echo isset($user->created_at) ? date('d/m/Y', strtotime($user->created_at)) : 'N/A'; ?></p>
                <p><strong>👑 Tipo de conta:</strong> <?php echo isset($user->user_type) ? ucfirst($user->user_type) : 'Cliente'; ?></p>
            </div>
            
            <div class="info-card">
                <h3>🔒 Alterar Senha</h3>
                <form action="index.php?action=change_password" method="POST">
                    <div class="form-group">
                        <label for="current_password">🔐 Senha Atual</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">🆕 Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">✅ Confirmar Nova Senha</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">🔒 Alterar Senha</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const zipCodeInput = document.getElementById('zip_code');
    const phoneInput = document.getElementById('phone');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // --- LÓGICA DO VIACEP ATUALIZADA ---
    const addressInput = document.getElementById('address');
    const numberInput = document.getElementById('number'); // Campo de número
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
                        numberInput.focus(); // Move o cursor para o campo de número!
                    } else {
                        Swal.fire({ icon: 'info', title: 'CEP não encontrado', confirmButtonColor: 'var(--primary-color)' });
                        addressInput.value = '';
                        cityInput.value = '';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    Swal.fire({ icon: 'error', title: 'Erro', text: 'Ocorreu um erro ao buscar o CEP.', confirmButtonColor: 'var(--primary-color)' });
                    addressInput.value = '';
                    cityInput.value = '';
                });
        });
    }
    // --- FIM DA LÓGICA DO VIACEP ---
    
    // --- Máscaras e Validações ---
    if (zipCodeInput) {
        zipCodeInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '').slice(0, 8);
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            this.value = value;
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '').slice(0, 11);
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            }
            this.value = value;
        });
    }
    
    const passwordForm = document.querySelector('form[action*="change_password"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Erro', text: 'As senhas não coincidem!', confirmButtonColor: 'var(--primary-color)' });
                confirmPassword.focus();
            }
        });
    }
});
</script>

<style>
    /* Adiciona um estilo para o form-row funcionar bem */
    .form-row {
        display: flex;
        gap: 1rem;
    }
</style>

<?php 
if (file_exists('../views/layout/footer.php')) {
    include '../views/layout/footer.php';
} else {
    include $_SERVER['DOCUMENT_ROOT'] . '/tsukuyomi/views/layout/footer.php';
}
?>