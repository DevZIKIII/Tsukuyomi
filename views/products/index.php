<?php include '../views/layout/header.php'; ?>

<div class="hero fade-in">
    <h1>Tsukuyomi Streetwear</h1>
    <p>Vista-se com o poder dos animes</p>
    <a href="#products" class="btn btn-primary">Explorar Coleção</a>
</div>

<section id="products">
    <h2>Nossos Produtos</h2>
    
    <?php if(isset($keywords) && !empty($keywords)): ?>
        <p>Resultados para: <strong><?php echo htmlspecialchars($keywords); ?></strong></p>
    <?php endif; ?>
    
    <div class="products-grid">
        <?php foreach($products as $product): ?>
            <div class="product-card fade-in">
                <a href="/tsukuyomi/public/index.php?action=product&id=<?php echo $product['id']; ?>">
                    <!-- Imagem aqui - <?php echo $product['name']; ?> -->
                    <img src="<?php echo BASE_URL; ?>images/products/<?php echo $product['image_url']; ?>"
                        alt="<?php echo $product['name']; ?>"
                        class="product-image"
                        onerror="this.src='<?php echo BASE_URL; ?>images/placeholder.jpg'">
                </a>
                
                <div class="product-info">
                    <div class="product-category"><?php echo $product['category']; ?></div>
                    <h3 class="product-name">
                        <a href="/tsukuyomi/public/index.php?action=product&id=<?php echo $product['id']; ?>">
                            <?php echo $product['name']; ?>
                        </a>
                    </h3>
                    <div class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></div>
                    <div class="product-size">Tamanho: <?php echo $product['size']; ?></div>
                    
                    <?php if($product['stock_quantity'] > 0): ?>
                        <div class="product_add_cart">
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary">
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Esgotado</button>
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
        <p style="text-align: center; margin: 3rem 0;">Nenhum produto encontrado.</p>
    <?php endif; ?>
</section>

<?php include '../views/layout/footer.php'; ?>