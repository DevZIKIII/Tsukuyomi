<?php include '../views/layout/header.php'; ?>

<div class="hero scale-in">
    <h1>Tsukuyomi Streetwear</h1>
    <p>✨ Vista-se com o poder dos animes ✨</p>
    <a href="#products" class="btn btn-primary">Explorar Coleção</a>
</div>

<section id="products" class="fade-in">
    <h2>Nossos Produtos</h2>
    
    <?php if(isset($keywords) && !empty($keywords)): ?>
        <div class="alert" style="background: rgba(139, 92, 246, 0.1); border-color: var(--primary-color); color: var(--primary-color);">
            🔍 Resultados para: <strong><?php echo htmlspecialchars($keywords); ?></strong>
        </div>
    <?php endif; ?>
    
    <div class="products-grid">
        <?php foreach($products as $index => $product): ?>
            <div class="product-card fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                <a href="/tsukuyomi/public/index.php?action=product&id=<?php echo $product['id']; ?>">
                    <img src="<?php echo BASE_URL; ?>images/products/<?php echo $product['image_url']; ?>"
                        alt="<?php echo $product['name']; ?>"
                        class="product-image"
                        onerror="this.src='<?php echo BASE_URL; ?>images/placeholder.jpg'"
                        loading="lazy">
                </a>
                
                <div class="product-info">
                    <div class="product-category"><?php echo $product['category']; ?></div>
                    <h3 class="product-name">
                        <a href="/tsukuyomi/public/index.php?action=product&id=<?php echo $product['id']; ?>">
                            <?php echo $product['name']; ?>
                        </a>
                    </h3>
                    <div class="product-price">💰 R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                    <div class="product-size">Tamanho: <?php echo $product['size']; ?></div>
                    
                    <?php if($product['stock_quantity'] > 0): ?>
                        <div class="product_add_cart">
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary">
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>❌ Esgotado</button>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                        <div style="margin-top: 1rem;">
                            <a href="/tsukuyomi/public/index.php?action=edit_product&id=<?php echo $product['id']; ?>" 
                               class="btn btn-secondary">Editar</a>
                            <a href="/tsukuyomi/public/index.php?action=delete_product&id=<?php echo $product['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if(empty($products)): ?>
        <div class="empty-cart fade-in">
            <h3>😔 Nenhum produto encontrado</h3>
            <p>Tente buscar por outros termos ou explore nossa coleção completa</p>
            <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-primary">Ver Todos os Produtos</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../views/layout/footer.php'; ?>