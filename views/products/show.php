<?php include '../views/layout/header.php'; ?>

<div class="product-detail">
    <div class="product-detail-container">
        <div class="product-detail-image">
            <!-- Imagem aqui - <?php echo $this->product->name; ?> -->
            <img src="/tsukuyomi/public/images/products/<?php echo $this->product->image_url; ?>" 
                 alt="<?php echo $this->product->name; ?>"
                 onerror="this.src='/tsukuyomi/public/images/placeholder.jpg'">
        </div>
        
        <div class="product-detail-info">
            <div class="product-category"><?php echo $this->product->category; ?></div>
            <h1><?php echo $this->product->name; ?></h1>
            
            <div class="product-price">R$ <?php echo number_format($this->product->price, 2, ',', '.'); ?></div>
            
            <div class="product-description">
                <h3>Descrição</h3>
                <p><?php echo nl2br($this->product->description); ?></p>
            </div>
            
            <div class="product-details">
                <p><strong>Tamanho:</strong> <?php echo $this->product->size; ?></p>
                <p><strong>Estoque:</strong> <?php echo $this->product->stock_quantity; ?> unidades</p>
            </div>
            
            <?php if($this->product->stock_quantity > 0): ?>
                <div class="product-actions">
                    <form onsubmit="event.preventDefault(); addToCart(<?php echo $this->product->id; ?>);">
                        <label for="quantity">Quantidade:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" 
                               max="<?php echo $this->product->stock_quantity; ?>" class="quantity-input">
                        <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                    </form>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Produto Esgotado</button>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                <div class="admin-actions">
                    <a href="/tsukuyomi/public/index.php?action=edit_product&id=<?php echo $this->product->id; ?>" 
                       class="btn btn-secondary">Editar Produto</a>
                    <a href="/tsukuyomi/public/index.php?action=delete_product&id=<?php echo $this->product->id; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir Produto</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="related-products">
        <h2>Produtos Relacionados</h2>
        <!-- Aqui você pode adicionar produtos relacionados -->
    </div>
</div>

<style>
.product-detail-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin: 2rem 0;
}

.product-detail-image img {
    width: 100%;
    border-radius: 1rem;
}

.product-detail-info {
    padding: 2rem;
}

.product-actions {
    margin: 2rem 0;
}

.admin-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

@media (max-width: 768px) {
    .product-detail-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../views/layout/footer.php'; ?>