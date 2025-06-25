<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Login</h2>
    
    <form action="/tsukuyomi/public/index.php?action=authenticate" method="POST">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Senha *</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Entrar</button>
        
        <p style="margin-top: 1rem; text-align: center;">
            Não tem uma conta? <a href="?action=register">Cadastre-se</a>
        </p>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>