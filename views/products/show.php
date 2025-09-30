<?php include '../views/layout/header.php'; ?>

<div class="product-detail-page">
    <div class="product-detail-container">
        <div class="product-image-gallery">
            <img src="images/products/<?php echo $product->image_url; ?>"
                 alt="<?php echo htmlspecialchars($product->name); ?>"
                 onerror="this.src='images/placeholder.jpg'">
        </div>
        
        <div class="product-info-panel">
            <span class="badge badge-secondary"><?php echo htmlspecialchars($product->category); ?></span>
            <h1><?php echo htmlspecialchars($product->name); ?></h1>
            <p class="product-price">R$ <?php echo number_format($product->price, 2, ',', '.'); ?></p>
            
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
                        <p>Nenhuma variação de tamanho encontrada.</p>
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
                    <a href="/tsukuyomi/public/index.php?action=delete_product&id=<?php echo $product->id; ?>" class="btn btn-danger" onclick="return confirm('Isso excluirá o produto e TODAS as suas variações. Tem certeza?')">Excluir</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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
            stockInfo.innerHTML = stock > 5
                ? `<span style="color: var(--success-color);">Em estoque</span> (${stock} unidades)`
                : (stock > 0 ? `<span style="color: var(--warning-color);">Estoque baixo!</span> (${stock} unidades)` : `<span style="color: var(--error-color);">Esgotado</span>`);
            
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
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Por favor, selecione um tamanho antes de adicionar ao carrinho.',
                confirmButtonColor: 'var(--primary-color)'
            });
            return;
        }

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('size', selectedSize);

        fetch('index.php?action=add_to_cart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartBadge = document.getElementById('cart-badge');
                if (cartBadge) {
                    cartBadge.textContent = data.cart_count;
                    cartBadge.style.display = 'inline-block';
                }
                // Simulação de sucesso no botão
                this.textContent = '✅ Adicionado!';
                setTimeout(() => {
                    this.textContent = 'Adicionar ao Carrinho';
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Erro ao adicionar ao carrinho.',
                    confirmButtonColor: 'var(--primary-color)'
                });
            }
        })
        .catch(error => console.error('Erro:', error));
    });
});
</script>

<?php include '../views/layout/footer.php'; ?>