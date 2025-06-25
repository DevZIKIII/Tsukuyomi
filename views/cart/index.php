<?php include '../views/layout/header.php'; ?>

<h2>Meu Carrinho</h2>

<div class="cart-container">
    <?php if(!empty($cart_items)): ?>
        <div class="cart-items">
            <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <!-- Imagem aqui - <?php echo $item['name']; ?> -->
                    <img src="<?php echo BASE_URL; ?>images/products/<?php echo $item['image_url']; ?>"
                        alt="<?php echo $item['name']; ?>"
                        class="cart-item-image"
                        onerror="this.src='<?php echo BASE_URL; ?>images/placeholder.jpg'">
                    
                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Tamanho: <?php echo $item['size']; ?></p>
                        <p class="product-price">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                    </div>
                    
                    <div class="cart-item-actions">
                        <input type="number" 
                               value="<?php echo $item['quantity']; ?>" 
                               min="1" 
                               class="quantity-input"
                               onchange="updateCartQuantity(<?php echo $item['id']; ?>, this.value)">
                        
                        <p class="item-total">
                            Total: R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?>
                        </p>
                        
                        <a href="/tsukuyomi/public/index.php?action=remove_from_cart&id=<?php echo $item['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Remover este item do carrinho?')">Remover</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <h3>Resumo do Pedido</h3>
            <div class="cart-total">
                Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
            </div>
            
            <form action="/tsukuyomi/public/index.php?action=create_order" method="POST">
                <div class="form-group">
                    <label for="payment_method">Forma de Pagamento</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="credit_card">Cartão de Crédito</option>
                        <option value="pix">PIX</option>
                        <option value="boleto">Boleto</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="shipping_address">Endereço de Entrega</label>
                    <textarea name="shipping_address" id="shipping_address" 
                              class="form-control" rows="3" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Finalizar Pedido</button>
            </form>
            
            <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-secondary">Continuar Comprando</a>
            <a href="/tsukuyomi/public/index.php?action=clear_cart" class="btn btn-danger"
               onclick="return confirm('Limpar todo o carrinho?')">Limpar Carrinho</a>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem;">
            <h3>Seu carrinho está vazio</h3>
            <p>Adicione alguns produtos incríveis!</p>
            <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-primary">Ver Produtos</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../views/layout/footer.php'; ?>