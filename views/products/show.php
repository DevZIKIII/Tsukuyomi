<?php include '../views/layout/header.php'; ?>

<div class="product-detail-page">
    <div class="product-detail-container">
        <div class="product-image-gallery">
            <img src="<?php echo BASE_URL; ?>images/products/<?php echo $product->image_url; ?>"
                 alt="<?php echo htmlspecialchars($product->name); ?>"
                 onerror="this.src='<?php echo BASE_URL; ?>images/placeholder.jpg'">
        </div>
        
        <div class="product-info-panel">
            <span class="product-category-tag"><?php echo htmlspecialchars($product->category); ?></span>
            <h1><?php echo htmlspecialchars($product->name); ?></h1>
            <p class="product-price-detail">R$ <?php echo number_format($product->price, 2, ',', '.'); ?></p>
            
            <div class="product-description-box">
                <h3>Descrição</h3>
                <p><?php echo nl2br(htmlspecialchars($product->description)); ?></p>
            </div>
            
            <div class="size-selector">
                <h3>Tamanho: <span id="selected-size-display"></span></h3>
                <div class="size-options" id="size-options-container">
                    <?php if (!empty($product->variants)): ?>
                        <?php foreach($product->variants as $variant): ?>
                            <button class="size-option <?php echo $variant['stock_quantity'] <= 0 ? 'disabled' : ''; ?>"
                                    data-size="<?php echo htmlspecialchars($variant['size']); ?>"
                                    data-stock="<?php echo $variant['stock_quantity']; ?>">
                                <?php echo htmlspecialchars($variant['size']); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhuma variação de tamanho encontrada para este produto.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="stock-info" id="stock-info">
                Selecione um tamanho para ver o estoque.
            </div>
            
            <div class="product-actions-detail">
                <button id="add-to-cart-btn" class="btn btn-primary btn-block" disabled>Selecione um Tamanho</button>
            </div>
            
            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                <div class="admin-actions-detail">
                    <a href="/tsukuyomi/public/index.php?action=edit_product&id=<?php echo $product->id; ?>" class="btn btn-secondary">Editar</a>
                    <a href="/tsukuyomi/public/index.php?action=delete_product&id=<?php echo $product->id; ?>" class="btn btn-danger" onclick="return confirm('Isso excluirá o produto base e TODAS as suas variações de tamanho. Tem certeza?')">Excluir</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-detail-page { max-width: 1200px; margin: 3rem auto; padding: 0 1rem; }
.product-detail-container { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: flex-start; }
.product-image-gallery img { width: 100%; border-radius: var(--border-radius-xl); box-shadow: var(--shadow-xl); }
.product-info-panel { display: flex; flex-direction: column; gap: 1.5rem; }
.product-category-tag { background: var(--surface-color); color: var(--primary-color); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; align-self: flex-start; }
.product-info-panel h1 { font-size: 2.5rem; margin: 0; }
.product-price-detail { font-size: 2rem; font-weight: bold; color: var(--primary-color); margin: 0; }
.product-description-box { background: var(--surface-color); padding: 1.5rem; border-radius: var(--border-radius-lg); }
.size-options { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.size-option { padding: 0.75rem 1.25rem; border: 2px solid var(--border-color); border-radius: var(--border-radius-lg); font-weight: 600; cursor: pointer; transition: all 0.2s ease; background: none; color: var(--text-primary); font-family: inherit; font-size: inherit; }
.size-option:hover { border-color: var(--primary-color); }
.size-option.active { border-color: var(--primary-color); background: var(--primary-color); color: white; }
.size-option.disabled { opacity: 0.5; cursor: not-allowed; background: var(--border-color); color: var(--text-secondary); pointer-events: none; }
.admin-actions-detail { display: flex; gap: 1rem; margin-top: 1rem; }
.btn-block { width: 100%; }
@media(max-width: 768px) { .product-detail-container { grid-template-columns: 1fr; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedSize = null;
    const sizeButtons = document.querySelectorAll('.size-option');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const stockInfo = document.getElementById('stock-info');
    const productId = <?php echo $product->id; ?>;

    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            selectedSize = this.dataset.size;
            const stock = parseInt(this.dataset.stock);

            document.getElementById('selected-size-display').textContent = selectedSize;
            stockInfo.innerHTML = stock > 0 
                ? `<span style="color: var(--success-color);">Em estoque</span> (${stock} unidades)`
                : `<span style="color: var(--error-color);">Esgotado</span>`;
            
            if (stock > 0) {
                addToCartBtn.disabled = false;
                addToCartBtn.textContent = 'Adicionar ao Carrinho';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'Esgotado';
            }
        });
    });

    addToCartBtn.addEventListener('click', function() {
        if (!selectedSize) {
            alert('Por favor, selecione um tamanho.');
            return;
        }

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('size', selectedSize);

        const button = this;
        button.innerHTML = '⏳ Adicionando...';
        button.disabled = true;

        fetch('index.php?action=add_to_cart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.innerHTML = '✅ Adicionado!';
                // Atualiza o contador do carrinho no header
                const cartBadge = document.querySelector('.cart-badge');
                if (cartBadge) {
                    cartBadge.textContent = data.cart_count;
                }
                setTimeout(() => {
                    button.innerHTML = 'Adicionar ao Carrinho';
                    button.disabled = false;
                }, 2000);
            } else {
                alert(data.message || 'Erro ao adicionar ao carrinho.');
                button.innerHTML = 'Adicionar ao Carrinho';
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro de conexão ao adicionar ao carrinho.');
            button.innerHTML = 'Adicionar ao Carrinho';
            button.disabled = false;
        });
    });
});
</script>

<?php include '../views/layout/footer.php'; ?>