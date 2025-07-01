<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Editar Usuário</h2>
    
    <form action="index.php?action=update_user&id=<?php echo $user['id']; ?>" method="POST">
        <div class="form-group">
            <label for="name">Nome Completo *</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Telefone</label>
            <input type="tel" id="phone" name="phone" class="form-control" 
                   value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        
        <div class="form-group">
            <label for="address">Endereço</label>
            <input type="text" id="address" name="address" class="form-control"
                   value="<?php echo htmlspecialchars($user['address']); ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="city">Cidade</label>
                <input type="text" id="city" name="city" class="form-control"
                       value="<?php echo htmlspecialchars($user['city']); ?>">
            </div>
            
            <div class="form-group half">
                <label for="state">Estado</label>
                <select id="state" name="state" class="form-control">
                    <option value="">Selecione</option>
                    <?php
                    $states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                    foreach($states as $state): ?>
                        <option value="<?php echo $state; ?>" <?php echo $user['state'] == $state ? 'selected' : ''; ?>>
                            <?php echo $state; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="zip_code">CEP</label>
            <input type="text" id="zip_code" name="zip_code" class="form-control"
                   value="<?php echo htmlspecialchars($user['zip_code']); ?>">
        </div>
        
        <div class="form-group">
            <label for="user_type">Tipo de Usuário *</label>
            <select id="user_type" name="user_type" class="form-control" required>
                <option value="customer" <?php echo $user['user_type'] == 'customer' ? 'selected' : ''; ?>>Cliente</option>
                <option value="admin" <?php echo $user['user_type'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="new_password">Nova Senha (deixe em branco para manter a atual)</label>
            <input type="password" id="new_password" name="new_password" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
        <a href="index.php?action=users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>