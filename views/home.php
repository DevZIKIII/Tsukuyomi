<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsukuyomi - Streetwear Geek</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../public/css/style.css">
    
    <!-- Fonte Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="container nav-container">
                <a href="index.php" class="logo">Tsukuyomi</a>
                
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?action=products">Produtos</a></li>
                    <li><a href="#about">Sobre</a></li>
                    <li><a href="#contact">Contato</a></li>
                </ul>
                
                <div class="nav-actions">
                    <form class="search-form" method="GET" action="index.php">
                        <input type="hidden" name="action" value="search">
                        <input type="text" name="q" placeholder="Buscar..." class="search-input">
                    </form>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php 
                        // Definir cart_count na sessão para evitar múltiplas consultas
                        if (!isset($_SESSION['cart_count'])) {
                            $_SESSION['cart_count'] = 0;
                        }
                        ?>
                        <a href="index.php?action=cart" class="btn btn-secondary btn-sm">
                            Carrinho
                            <?php if($_SESSION['cart_count'] > 0): ?>
                                <span class="cart-badge"><?php echo $_SESSION['cart_count']; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="user-menu">
                            <span class="user-name">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <div class="user_action">
                                <ul>
                                    <li><a href="index.php?action=profile">Perfil</a></li>
                                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                        <li><a href="index.php?action=users">Usuários</a></li>
                                    <?php endif; ?>
                                    <li><a href="index.php?action=orders">Pedidos</a></li>
                                    <li><a href="index.php?action=logout">Sair</a></li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?action=login" class="btn btn-secondary btn-sm">Login</a>
                        <a href="index.php?action=register" class="btn btn-primary btn-sm">Cadastrar</a>
                    <?php endif; ?>
                    
                    <button class="menu-toggle">☰</button>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container">
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