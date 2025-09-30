<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="description" content="Tsukuyomi - A melhor loja de streetwear geek do Brasil">
    <title>Tsukuyomi - Streetwear Geek</title>
    <link rel="stylesheet" href="css/style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container nav-container">
            <a href="/tsukuyomi/public/index.php" class="logo">
                <img src="../public/images/logo2.0.png" alt="Tsukuyomi Logo">
            </a>
            
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php?action=vote">🗳️ Vote na Próxima Coleção</a></li>
                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                        <li><a href="index.php?action=sales_dashboard">📊 Dashboard</a></li>
                        <li><a href="index.php?action=all_orders">📦 Pedidos</a></li>
                        <li><a href="index.php?action=users">👥 Usuários</a></li>
                        <li><a href="index.php?action=coupons">🎫 Cupons</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="nav-actions-desktop">
                <form class="search-form" method="GET" action="/tsukuyomi/public/index.php">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="q" placeholder="Buscar..." class="search-input">
                </form>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/tsukuyomi/public/index.php?action=cart" class="btn btn-sm btn-secondary cart-button">
                        🛒
                        <span class="cart-badge" id="cart-badge" style="<?php echo (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) ? '' : 'display: none;'; ?>">
                            <?php echo $_SESSION['cart_count'] ?? 0; ?>
                        </span>
                    </a>
                    <div class="user-menu">
                        <span class="user-name">Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                        <div class="user-dropdown">
                            <a href="/tsukuyomi/public/index.php?action=profile">Meu Perfil</a>
                            <a href="/tsukuyomi/public/index.php?action=orders">Meus Pedidos</a>
                             <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                <hr>
                                <a href="index.php?action=create_product">Add Produto</a>
                                <a href="index.php?action=admin_votes">Votações</a>
                                <a href="index.php?action=export">Exportar</a>
                            <?php endif; ?>
                            <hr>
                            <a href="/tsukuyomi/public/index.php?action=logout">Sair</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/tsukuyomi/public/index.php?action=login" class="btn btn-sm btn-secondary">Login</a>
                    <a href="/tsukuyomi/public/index.php?action=register" class="btn btn-sm btn-primary">Cadastrar</a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" aria-label="Abrir menu">☰</button>
        </div>
        
        <div class="mobile-menu-container">
            <nav class="mobile-main-nav">
                <ul style="list-style: none;" class="mobile-nav-links">
                    <li><a href="index.php?action=vote">🗳️ Vote na Próxima Coleção</a></li>
                     <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                        <li><a href="index.php?action=sales_dashboard">📊 Dashboard</a></li>
                        <li><a href="index.php?action=all_orders">📦 Pedidos</a></li>
                        <li><a href="index.php?action=users">👥 Usuários</a></li>
                        <li><a href="index.php?action=coupons">🎫 Cupons</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="nav-actions-mobile">
                 <form class="search-form" method="GET" action="/tsukuyomi/public/index.php">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="q" placeholder="Buscar..." class="search-input">
                </form>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="/tsukuyomi/public/index.php?action=login" class="btn btn-secondary btn-block">Login</a>
                    <a href="/tsukuyomi/public/index.php?action=register" class="btn btn-primary btn-block">Cadastrar</a>
                <?php else: ?>
                    <a href="/tsukuyomi/public/index.php?action=profile" class="btn btn-secondary btn-block">Meu Perfil</a>
                    <a href="/tsukuyomi/public/index.php?action=logout" class="btn btn-danger btn-block">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main class="container" id="main-content">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>