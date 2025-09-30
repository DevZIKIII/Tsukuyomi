<?php include '../views/layout/header.php'; ?>

<div class="hero">
    <h1>Tsukuyomi Streetwear</h1>
    <p>✨ Vista-se com o poder dos animes ✨</p>
    <a href="#products" style="margin-top: 1rem;" class="btn btn-primary">Explorar Coleção</a>
</div>

<section id="products">
    <h2>Nossos Produtos</h2>
    
    <?php if(isset($keywords) && !empty($keywords)): ?>
        <div class="alert">
            🔍 Resultados para: <strong><?php echo htmlspecialchars($keywords); ?></strong>
        </div>
    <?php endif; ?>
    
    <div class="products-grid">
        <?php foreach($products as $product): ?>
            <a href="/tsukuyomi/public/index.php?action=product&id=<?php echo $product['id']; ?>" class="product-card">
                <img src="images/products/<?php echo $product['image_url']; ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="product-image"
                    onerror="this.src='images/placeholder.jpg'"
                    loading="lazy">
            
                <div class="product-info">
                    <div class="product-category"><?php echo $product['category']; ?></div>
                    <h3 class="product-name">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>
                    <div class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                    
                    <div class="btn btn-primary btn-sm">Ver Detalhes</div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php if(empty($products)): ?>
        <div class="empty-cart">
            <h3>😔 Nenhum produto encontrado</h3>
            <p>Tente buscar por outros termos ou explore nossa coleção completa.</p>
            <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-primary">Ver Todos os Produtos</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../views/layout/footer.php'; ?>