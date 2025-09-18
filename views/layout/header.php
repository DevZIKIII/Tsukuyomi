<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Tsukuyomi - A melhor loja de streetwear geek do Brasil">
    <title>Tsukuyomi - Streetwear Geek</title>
    
    <!-- CSS com caminho absoluto -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Fonte Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Skip link for accessibility -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    
    <header>
        <nav>
            <div class="container nav-container">
                <a href="/tsukuyomi/public/index.php" class="logo" style="width: 300px; height: 100px;">
                    <img src="../public/images/logo2.0.png" 
                         alt="Tsukuyomi Logo" 
                         style="width: 250px; height: 250px; margin-top: -3em;"
                         loading="eager">
                </a>                
                <ul class="nav-links">
                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                        <li><a href="index.php?action=create_product">Adicionar Produto</a></li>
                        <li><a href="index.php?action=users">Usuários</a></li>
                        <li><a href="index.php?action=coupons">Cupons</a></li>
                        <li><a href="index.php?action=all_orders">Pedidos</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="nav-actions">
                    <form class="search-form" method="GET" action="/tsukuyomi/public/index.php">
                        <input type="hidden" name="action" value="search">
                        <input type="text" name="q" placeholder="Buscar..." class="search-input">
                    </form>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/tsukuyomi/public/index.php?action=cart" class="btn btn-secondary btn-sm">
                            🛒 Carrinho
                            <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                <span class="cart-badge"><?php echo $_SESSION['cart_count']; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="user-menu">
                            <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <div class="user_action">
                                <a href="/tsukuyomi/public/index.php?action=profile">Perfil</a>
                                <!-- <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                    <a href="/tsukuyomi/public/index.php?action=users">Usuários</a>
                                <?php endif; ?> -->
                                <a href="/tsukuyomi/public/index.php?action=orders">Pedidos</a>
                                <a href="/tsukuyomi/public/index.php?action=logout">Sair</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/tsukuyomi/public/index.php?action=login" class="btn btn-secondary btn-sm">Login</a>
                        <a href="/tsukuyomi/public/index.php?action=register" class="btn btn-primary btn-sm">Cadastrar</a>
                    <?php endif; ?>
                    
                    <button class="menu-toggle">☰</button>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container" id="main-content">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success fade-in">
                <?php 
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error fade-in">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>