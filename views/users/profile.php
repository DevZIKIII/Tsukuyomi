<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Meu Perfil</h2>
    
    <form action="/tsukuyomi/public/index.php?action=update_profile" method="POST">
        <div class="form-group">
            <label for="name">Nome Completo *</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($this->user->name); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($this->user->email); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Telefone</label>
            <input type="tel" id="phone" name="phone" class="form-control" 
                   value="<?php echo htmlspecialchars($this->user->phone); ?>">
        </div>
        
        <div class="form-group">
            <label for="address">Endereço</label>
            <input type="text" id="address" name="address" class="form-control"
                   value="<?php echo htmlspecialchars($this->user->address); ?>">
        </div>
        
        <div class="form-group">
            <label for="city">Cidade</label>
            <input type="text" id="city" name="city" class="form-control"
                   value="<?php echo htmlspecialchars($this->user->city); ?>">
        </div>
        
        <div class="form-group">
            <label for="state">Estado</label>
            <select id="state" name="state" class="form-control">
                <option value="">Selecione</option>
                <?php
                $states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                foreach($states as $state): ?>
                    <option value="<?php echo $state; ?>" <?php echo $this->user->state == $state ? 'selected' : ''; ?>>
                        <?php echo $state; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="zip_code">CEP</label>
            <input type="text" id="zip_code" name="zip_code" class="form-control"
                   value="<?php echo htmlspecialchars($this->user->zip_code); ?>">
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
        <a href="/tsukuyomi/public/index.php?action=orders" class="btn btn-secondary">Meus Pedidos</a>
    </form>
    
    <div style="margin-top: 2rem; text-align: center;">
        <p><small>Membro desde: <?php echo date('d/m/Y', strtotime($this->user->created_at)); ?></small></p>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>