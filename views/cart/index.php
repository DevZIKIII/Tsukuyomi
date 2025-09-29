<?php include '../views/layout/header.php'; ?>

<div class="page-container">

    <div class="checkout-container">

        <?php if(!empty($cart_items)): ?>

            <div id="initial-cart-view">
                <h1 class="main-title">🛒 Meu Carrinho</h1>
                <div class="initial-cart-layout">
                    <div class="cart-items-list">
                        <?php foreach($cart_items as $index => $item): ?>
                            <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                                <img src="images/products/<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image" onerror="this.src='images/placeholder.jpg'" loading="lazy">
                                <div class="cart-item-info">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p>Tamanho: <strong><?php echo $item['size']; ?></strong></p>
                                    <p class="product-price">R$ <span class="item-price"><?php echo number_format($item['price'], 2, ',', '.'); ?></span></p>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn minus" onclick="changeQuantity(<?php echo $item['id']; ?>, -1, <?php echo $item['price']; ?>)">-</button>
                                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="quantity-input" id="quantity-<?php echo $item['id']; ?>" onchange="updateCartItemOnServer(<?php echo $item['id']; ?>, this.value)">
                                        <button class="quantity-btn plus" onclick="changeQuantity(<?php echo $item['id']; ?>, 1, <?php echo $item['price']; ?>)">+</button>
                                    </div>
                                    <p class="item-total">Total: R$ <span id="item-total-<?php echo $item['id']; ?>"><?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span></p>
                                    <a href="index.php?action=remove_from_cart&id=<?php echo $item['id']; ?>" class="btn-remove" onclick="return confirm('Remover este item do carrinho?')">Remover</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary-initial">
                         <h3>Resumo do Pedido</h3>
                         <div class="summary-line">
                            <span>Subtotal:</span>
                            <span id="initial-subtotal">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                         <div class="summary-line">
                            <span>Frete:</span>
                            <span>A calcular no checkout</span>
                        </div>
                        <hr class="summary-divider-initial">
                         <div class="cart-total">
                            <span>Total (parcial):</span>
                            <span id="initial-total">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                        <button class="btn btn-primary btn-block" onclick="startCheckout()">✨ IR PARA O CHECKOUT</button>
                         <a href="index.php?action=products" class="btn btn-secondary btn-block">🛍️ CONTINUAR COMPRANDO</a>
                    </div>
                </div>
            </div>

            <div id="checkout-view" style="display: none;">
                <div class="checkout-header">
                    <h1>Finalizar Compra</h1>
                    <p>Complete as etapas abaixo para finalizar seu pedido</p>
                </div>

                <div class="progress-steps">
                    <div class="step active" data-step="1"><div class="step-circle">🛒</div><div class="step-label">Revisão</div></div>
                    <div class="step" data-step="2"><div class="step-circle">📍</div><div class="step-label">Endereço</div></div>
                    <div class="step" data-step="3"><div class="step-circle">💳</div><div class="step-label">Pagamento</div></div>
                    <div class="step" data-step="4"><div class="step-circle">✅</div><div class="step-label">Confirmação</div></div>
                    <div class="progress-line-bg">
                        <div class="progress-line-fg" id="progress-line-fg"></div>
                    </div>
                </div>

                <div class="checkout-content">
                    <div class="step-content">
                        <div class="step-panel active" id="step-1">
                            <h2>Revise os itens para compra</h2>
                            <div class="review-items-list" id="review-cart-items"></div>
                            <div class="coupon-section">
                                 <h3>Cupom de Desconto</h3>
                                 <div id="coupon-area" class="coupon-input-group">
                                    <input type="text" class="form-control" id="coupon-code" placeholder="Digite o código do cupom">
                                    <button class="btn btn-secondary" onclick="applyCoupon()">APLICAR</button>
                                 </div>
                            </div>
                            <div class="navigation-buttons">
                                <a href="index.php?action=products" class="btn btn-secondary">← CONTINUAR COMPRANDO</a>
                                <button class="btn btn-primary" onclick="nextStep()" id="next-1">PRÓXIMO: ENDEREÇO →</button>
                            </div>
                        </div>

                        <div class="step-panel" id="step-2">
                             <h2>Endereço de Entrega</h2>
                             <form class="address-form" id="address-form">
                                <div class="form-group">
                                    <label for="cep">CEP *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000" maxlength="9" required>
                                        <button type="button" class="btn btn-secondary" onclick="searchCEP()">BUSCAR</button>
                                    </div>
                                </div>
                                 <div class="form-row">
                                     <div class="form-group" style="flex: 3;"><label for="street">Rua/Avenida *</label><input type="text" class="form-control" id="street" name="street" required></div>
                                     <div class="form-group" style="flex: 1;"><label for="number">Número *</label><input type="text" class="form-control" id="number" name="number" required></div>
                                 </div>
                                 <div class="form-group"><label for="complement">Complemento</label><input type="text" class="form-control" id="complement" name="complement" placeholder="Apartamento, bloco, etc (opcional)"></div>
                                 <div class="form-row">
                                     <div class="form-group" style="flex: 2;"><label for="neighborhood">Bairro *</label><input type="text" class="form-control" id="neighborhood" name="neighborhood" required></div>
                                     <div class="form-group" style="flex: 2;"><label for="city">Cidade *</label><input type="text" class="form-control" id="city" name="city" required></div>
                                     <div class="form-group" style="flex: 1;"><label for="state">Estado *</label><select class="form-control" id="state" name="state" required><option value="">UF</option><option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option><option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option><option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option><option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option><option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option><option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option></select></div>
                                 </div>
                             </form>
                             <div class="navigation-buttons">
                                <button class="btn btn-secondary" onclick="previousStep()">← VOLTAR</button>
                                <button class="btn btn-primary" onclick="nextStep()" id="next-2">PRÓXIMO: PAGAMENTO →</button>
                             </div>
                        </div>

                        <div class="step-panel" id="step-3">
                            <h2>Forma de Pagamento</h2>
                            <form id="payment-form">
                                <div class="payment-methods">
                                     <div class="payment-method" onclick="selectPayment('card')" data-method="card"><div class="payment-icon">💳</div><div class="payment-label">Cartão</div></div>
                                     <div class="payment-method" onclick="selectPayment('pix')" data-method="pix"><div class="payment-icon">📱</div><div class="payment-label">PIX</div></div>
                                     <div class="payment-method" onclick="selectPayment('boleto')" data-method="boleto"><div class="payment-icon">📄</div><div class="payment-label">Boleto</div></div>
                                </div>
                                <div class="payment-details" id="card-details">
                                    <h4>Dados do Cartão</h4>
                                    <div class="form-row"><div class="form-group"><label for="card_number">Número do Cartão</label><input type="text" id="card_number" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" oninput="formatCardNumber(this)"></div></div>
                                    <div class="form-row"><div class="form-group"><label for="card_name">Nome no Cartão</label><input type="text" id="card_name" name="card_name" class="form-control" placeholder="NOME COMO ESTÁ NO CARTÃO" style="text-transform: uppercase;"></div></div>
                                    <div class="form-row">
                                        <div class="form-group"><label for="card_expiry">Validade</label><input type="text" id="card_expiry" name="card_expiry" class="form-control" placeholder="MM/AA" maxlength="5" oninput="formatExpiry(this)"></div>
                                        <div class="form-group"><label for="card_cvv">CVV</label><input type="text" id="card_cvv" name="card_cvv" class="form-control" placeholder="123" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                                    </div>
                                    <div class="form-row"><div class="form-group"><label for="card_cpf">CPF do Titular</label><input type="text" id="card_cpf" name="card_cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" oninput="formatCPF(this)"></div></div>
                                </div>
                                <div class="payment-details" id="pix-details"><div class="payment-info"><h4>Pagamento via PIX</h4><p>Ao finalizar o pedido, você receberá um QR Code para pagamento instantâneo. O prazo para pagamento é de 30 minutos.</p></div></div>
                                <div class="payment-details" id="boleto-details"><div class="payment-info"><h4>Pagamento via Boleto</h4><p>O boleto será gerado após a confirmação do pedido. O prazo de pagamento é de 3 dias úteis.</p></div></div>
                            </form>
                            <div class="navigation-buttons">
                                <button class="btn btn-secondary" onclick="previousStep()">← VOLTAR</button>
                                <button class="btn btn-primary" onclick="nextStep()" id="next-3">PRÓXIMO: REVISAR PEDIDO →</button>
                            </div>
                        </div>

                        <div class="step-panel" id="step-4">
                            <h2>Revise e Confirme seu Pedido</h2>
                            <div id="order-review"></div>
                            <div class="navigation-buttons">
                                <button class="btn btn-secondary" onclick="previousStep()">← VOLTAR</button>
                                <form action="index.php?action=create_order" method="POST" id="final-checkout-form" onsubmit="prepareFinalForm()">
                                     <input type="hidden" name="shipping_address" id="shipping_address_hidden">
                                     <input type="hidden" name="payment_method" id="payment_method_hidden">
                                     <button type="submit" class="btn btn-success" id="confirm-order">🎉 FINALIZAR COMPRA</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="order-summary">
                        <h3 class="summary-title">Resumo do Pedido</h3>
                        <div class="summary-items" id="summary-items"></div>
                        <div class="summary-divider"></div>
                        <div class="summary-row"><span>Subtotal:</span><span id="subtotal">R$ 0,00</span></div>
                        <div class="summary-row discount" id="discount-row" style="display: none;"><span>Desconto:</span><span id="discount">-R$ 0,00</span></div>
                        <div class="summary-row"><span>Frete:</span><span id="shipping">A calcular</span></div>
                        <div class="summary-divider"></div>
                        <div class="summary-row total"><span>Total:</span><span id="total">R$ 0,00</span></div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <h3>🛒 Seu carrinho está vazio</h3>
                <p>✨ Adicione alguns produtos incríveis!</p>
                <a href="index.php?action=products" class="btn btn-primary">🛍️ Ver Produtos</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
        /* RESET BÁSICO */
    .checkout-container *, .checkout-container *::before, .checkout-container *::after { box-sizing: border-box; }
    
    /* NOVO CONTAINER DE PÁGINA */
    .page-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    
    /* LAYOUT INICIAL DO CARRINHO */
    .main-title { font-size: 2rem; margin-bottom: 2rem; }
    .initial-cart-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 2rem; align-items: flex-start; }
    .cart-items-list { display: flex; flex-direction: column; gap: 1.5rem; }
    .cart-item { display: grid; grid-template-columns: 100px 1fr auto; gap: 1.5rem; align-items: center; background: var(--surface-color); padding: 1.5rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); }
    .cart-item-image { width: 100px; height: 100px; object-fit: cover; border-radius: var(--border-radius-md); }
    .cart-item-info h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; }
    .cart-item-info p { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }
    .product-price { font-weight: bold; font-size: 1.2rem; color: var(--primary-color); }
    .cart-item-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 0.75rem; }
    .quantity-controls { display: flex; align-items: center; gap: 0.5rem; }
    .quantity-input { width: 50px; text-align: center; }
    .item-total { font-weight: bold; }
    .btn-remove { font-size: 0.8rem; color: var(--error-color); background: none; border: none; cursor: pointer; text-transform: uppercase; }
    .cart-summary-initial { background: var(--surface-color); padding: 2rem; border-radius: var(--border-radius-lg); position: sticky; top: 120px; border: 1px solid var(--border-color); }
    .cart-summary-initial h3 { text-align: center; margin-bottom: 1.5rem; }
    .summary-divider-initial { border: none; height: 1px; background-color: var(--border-color); margin: 1rem 0; }
    .cart-summary-initial .btn { margin-top: 1rem; }
    .cart-total { font-size: 1.2rem; font-weight: bold; }

    /* LAYOUT GERAL DO CHECKOUT */
    .checkout-header { text-align: center; margin-bottom: 2.5rem; }
    .checkout-header h1 { font-size: 2.25rem; margin-bottom: 0.5rem; }
    .checkout-header p { color: var(--text-secondary); }
    .checkout-content { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 2rem; align-items: flex-start; }
    .step-content { background: var(--surface-color); border-radius: var(--border-radius-xl); padding: 2.5rem; border: 1px solid var(--border-color); }
    .step-panel { display: none; animation: fadeIn 0.5s ease; }
    .step-panel.active { display: block; }
    .step-panel h2 { font-size: 1.5rem; margin-bottom: 2rem; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* BARRA DE PROGRESSO (CORRIGIDA) */
    .progress-steps { display: grid; grid-template-columns: repeat(4, 1fr); align-items: center; position: relative; margin-bottom: 3rem; }
    .step { text-align: center; position: relative; z-index: 2; }
    .step-circle { width: 40px; height: 40px; border-radius: 50%; background: var(--surface-color); border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; transition: all 0.4s ease; font-size: 1.25rem; }
    .step-label { color: var(--text-secondary); font-weight: 500; font-size: 0.8rem; }
    .step.active .step-circle { border-color: var(--primary-color); color: var(--primary-color); }
    .step.active .step-label { color: var(--text-primary); }
    .step.completed .step-circle { border-color: var(--success-color); background: var(--success-color); color: white; }
    .progress-line-bg { position: absolute; top: 19px; left: 12.5%; width: 75%; height: 2px; background: var(--border-color); z-index: 1; border-radius: 1px; }
    .progress-line-fg { background: var(--primary-color); height: 100%; width: 0; transition: width 0.4s ease; border-radius: 1px; }

    /* FORMULÁRIOS */
    .form-row { display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
    .form-group { flex: 1; display: flex; flex-direction: column; min-width: 120px; }
    .form-group label { margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary); }
    .input-group { display: flex; gap: 0.5rem; }

    /* RESUMO DO PEDIDO (SIDEBAR) */
    .order-summary { background: var(--surface-color); border-radius: var(--border-radius-xl); padding: 2rem; border: 1px solid var(--border-color); position: sticky; top: 120px; }
    .summary-title { font-size: 1.25rem; margin-bottom: 1.5rem; }
    .summary-items { max-height: 250px; overflow-y: auto; padding-right: 10px; margin-bottom: 1rem; }
    .summary-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; margin-bottom: 0.75rem; }
    .summary-item span { flex: 1; }
    .summary-divider { height: 1px; background-color: var(--border-color); margin: 1.5rem 0; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; }
    .summary-row.total { font-size: 1.2rem; font-weight: bold; margin-top: 1rem; color: var(--primary-color); }

    /* ETAPA 1: REVISÃO */
    .review-items-list { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
    .review-item { display: flex; align-items: center; gap: 1rem; background: var(--background-color); padding: 1rem; border-radius: var(--border-radius-md); }
    .review-item-image { width: 60px; height: 60px; object-fit: cover; border-radius: var(--border-radius-sm); }
    .review-item-info { flex-grow: 1; }
    .review-item-info h4 { margin: 0 0 0.25rem; font-size: 1rem; }
    .coupon-section { margin-top: 2rem; background: var(--background-color); padding: 1.5rem; border-radius: var(--border-radius-md); }
    .coupon-section h3 { margin-bottom: 1rem; }
    .coupon-input-group { display: flex; gap: 1rem; }

    /* ETAPA 3: PAGAMENTO */
    .payment-methods { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
    .payment-method { text-align: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: var(--border-radius-lg); cursor: pointer; transition: all 0.2s ease; }
    .payment-method:hover { border-color: var(--primary-color); }
    .payment-method.selected { border-color: var(--primary-color); background: rgba(139, 92, 246, 0.1); }
    .payment-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .payment-details { display: none; animation: fadeIn 0.3s; margin-top: 1rem; background: var(--background-color); padding: 1.5rem; border-radius: var(--border-radius-md); }
    .payment-details.active { display: block; }
    .payment-info { color: var(--text-secondary); }
    .payment-info h4 { color: var(--text-primary); margin-bottom: 0.5rem; }
    
    /* BOTÕES */
    .navigation-buttons { display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; }
    .btn-block { width: 100%; text-align: center; }

    /* RESPONSIVIDADE */
    @media (max-width: 992px) {
        .initial-cart-layout, .checkout-content { grid-template-columns: 1fr; }
        .order-summary { position: static; margin-top: 2rem; }
    }
    @media (max-width: 768px) {
        .cart-item { grid-template-columns: 1fr; text-align: center; }
        .cart-item-image { margin: 0 auto; }
        .cart-item-actions { align-items: center; }
        .payment-methods { grid-template-columns: 1fr; gap: 0.5rem; }
    }
</style>

<script>
    let cartItemsData = <?php echo json_encode($cart_items); ?>;
    let currentStep = 1;
    let orderData = { items: [], coupon: null, address: {}, payment: {}, subtotal: 0, discount: 0, shipping: 15.00, total: 0 };

    document.addEventListener('DOMContentLoaded', function() {
        const cepInput = document.getElementById('cep');
        if (cepInput) {
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '').slice(0, 8);
                if (value.length > 5) {
                    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
                }
                e.target.value = value;
            });
        }
    });

    function startCheckout() {
        // CORREÇÃO: Lê a quantidade atual de cada item diretamente dos inputs na página.
        cartItemsData.forEach(item => {
            const quantityInput = document.getElementById(`quantity-${item.id}`);
            if (quantityInput) {
                // Atualiza o objeto de dados com o valor que o usuário está vendo.
                item.quantity = parseInt(quantityInput.value);
            }
        });

        // Agora, prossegue com o checkout usando os dados 100% atualizados.
        document.getElementById('initial-cart-view').style.display = 'none';
        document.getElementById('checkout-view').style.display = 'block';
        
        initializeCheckout();
    }

    function initializeCheckout() {
        orderData.items = JSON.parse(JSON.stringify(cartItemsData));
        loadReviewItems();
        updateSummary();
    }
    
    function changeQuantity(cartId, change, price) {
        const input = document.getElementById(`quantity-${cartId}`);
        const maxStock = parseInt(input.getAttribute('max')) || 999;
        let newValue = parseInt(input.value) + change;

        if (newValue < 1) newValue = 1;
        if (newValue > maxStock) {
            alert(`Desculpe, temos apenas ${maxStock} unidades em estoque.`);
            newValue = maxStock;
        }

        input.value = newValue;
        document.getElementById(`item-total-${cartId}`).textContent = (newValue * price).toFixed(2).replace('.', ',');
        
        updateCartItemOnServer(cartId, newValue);
        updateInitialSummary();
    }
    
    function updateInitialSummary() {
        let newTotal = Array.from(document.querySelectorAll('.item-total span')).reduce((sum, el) => {
            return sum + parseFloat(el.textContent.replace(/\./g, '').replace(',', '.'));
        }, 0);
        document.getElementById('initial-subtotal').textContent = `R$ ${newTotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('initial-total').textContent = `R$ ${newTotal.toFixed(2).replace('.', ',')}`;
    }

    // Esta função agora só se preocupa em notificar o servidor sobre a mudança.
    function updateCartItemOnServer(cartId, quantity) {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', quantity);
        fetch('index.php?action=update_cart', { method: 'POST', body: formData });
    }
    
    // O restante das funções (loadReviewItems, updateSummary, searchCEP, etc.) permanecem exatamente as mesmas.
    function loadReviewItems() {
        const reviewContainer = document.getElementById('review-cart-items');
        reviewContainer.innerHTML = '';
        orderData.items.forEach(item => {
            reviewContainer.innerHTML += `
                <div class="review-item">
                    <img src="images/products/${item.image_url}" alt="${item.name}" class="review-item-image" onerror="this.src='images/placeholder.jpg'">
                    <div class="review-item-info">
                        <h4>${item.name}</h4><p>${item.quantity} x R$ ${parseFloat(item.price).toFixed(2).replace('.',',')}</p>
                    </div>
                    <strong class="item-price">R$ ${(item.quantity * item.price).toFixed(2).replace('.',',')}</strong>
                </div>`;
        });
    }

    function updateSummary() {
        orderData.subtotal = orderData.items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
        orderData.discount = 0;
        if (orderData.coupon && orderData.coupon.valid) {
            let discountAmount = parseFloat(orderData.coupon.discount_amount) || 0;
            orderData.discount = discountAmount > orderData.subtotal ? orderData.subtotal : discountAmount;
        }
        orderData.total = (orderData.subtotal - orderData.discount) + orderData.shipping;
        
        const summaryItems = document.getElementById('summary-items');
        summaryItems.innerHTML = orderData.items.map(item => `
            <div class="summary-item">
                <span>${item.quantity}x ${item.name}</span>
                <strong>R$ ${(parseFloat(item.price) * item.quantity).toFixed(2).replace('.',',')}</strong>
            </div>`).join('');

        document.getElementById('subtotal').textContent = `R$ ${orderData.subtotal.toFixed(2).replace('.',',')}`;
        document.getElementById('discount-row').style.display = orderData.discount > 0 ? 'flex' : 'none';
        document.getElementById('discount').textContent = `-R$ ${orderData.discount.toFixed(2).replace('.',',')}`;
        document.getElementById('shipping').textContent = orderData.shipping > 0 ? `R$ ${orderData.shipping.toFixed(2).replace('.',',')}` : 'Grátis';
        document.getElementById('total').textContent = `R$ ${orderData.total.toFixed(2).replace('.',',')}`;
    }
    
     function applyCoupon() {
        const code = document.getElementById('coupon-code').value.toUpperCase();
        if (!code) return;
        const formData = new FormData();
        formData.append('code', code);
        formData.append('total', orderData.subtotal);
        fetch('index.php?action=validate_coupon', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.valid) {
                orderData.coupon = data;
                alert('Cupom aplicado: ' + data.description);
                document.getElementById('coupon-area').innerHTML = `<p style="color: var(--success-color); text-align: center;">Cupom <b>${data.code}</b> aplicado!</p>`;
            } else {
                alert(data.message || 'Cupom inválido.');
                orderData.coupon = null;
            }
            updateSummary();
        });
    }

    function nextStep() {
        if (!validateStep(currentStep)) return;
        if (currentStep < 4) { currentStep++; showStep(currentStep); }
    }

    function previousStep() {
        if (currentStep > 1) { currentStep--; showStep(currentStep); }
    }
    
    function showStep(step) {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');
        document.querySelectorAll('.step').forEach(s => {
            const sNum = parseInt(s.dataset.step);
            s.classList.remove('active', 'completed');
            if (sNum < step) s.classList.add('completed');
            if (sNum === step) s.classList.add('active');
        });
        updateProgressBar();
        if (step === 4) prepareOrderReview();
    }

    function updateProgressBar() {
        const progressPercentage = ((currentStep - 1) / 3) * 100;
        document.getElementById('progress-line-fg').style.width = `${progressPercentage}%`;
    }

    function validateStep(step) {
        if (step === 2) {
            const form = document.getElementById('address-form');
            if (!form.checkValidity()) {
                alert('Por favor, preencha todos os campos de endereço obrigatórios (*).');
                form.reportValidity();
                return false;
            }
            orderData.address = Object.fromEntries(new FormData(form));
        }
        if (step === 3 && !orderData.payment.method) {
            alert('Por favor, selecione uma forma de pagamento.');
            return false;
        }
        return true;
    }

    function selectPayment(method) {
        orderData.payment.method = method;
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
        document.querySelector(`[data-method="${method}"]`).classList.add('selected');
        document.querySelectorAll('.payment-details').forEach(el => el.classList.remove('active'));
        if(document.getElementById(`${method}-details`)) {
            document.getElementById(`${method}-details`).classList.add('active');
        }
    }

    function searchCEP() {
        const cep = document.getElementById('cep').value.replace(/\D/g, '');
        if (cep.length !== 8) return alert('CEP inválido.');
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (!data.erro) {
                document.getElementById('street').value = data.logradouro;
                document.getElementById('neighborhood').value = data.bairro;
                document.getElementById('city').value = data.localidade;
                document.getElementById('state').value = data.uf;
                document.getElementById('number').focus();
            } else { alert('CEP não encontrado.'); }
        });
    }

    function prepareOrderReview() {
        const a = orderData.address;
        const fullAddress = `${a.street}, ${a.number}${a.complement ? ' - '+a.complement : ''}<br>${a.neighborhood}, ${a.city} - ${a.state}<br>CEP: ${a.cep}`;
        document.getElementById('order-review').innerHTML = `
            <div class="review-section"><h3>Endereço de Entrega</h3><p>${fullAddress}</p></div>
            <div class="review-section"><h3>Forma de Pagamento</h3><p>${orderData.payment.method.charAt(0).toUpperCase() + orderData.payment.method.slice(1)}</p></div>`;
    }

    function prepareFinalForm() {
        const a = orderData.address;
        document.getElementById('shipping_address_hidden').value = `${a.street}, ${a.number}, ${a.complement || ''} - ${a.neighborhood}, ${a.city} - ${a.state}, CEP: ${a.cep}`;
        document.getElementById('payment_method_hidden').value = orderData.payment.method;
    }
    
    function formatCardNumber(input) { let v = input.value.replace(/\D/g, '').slice(0,16); v = v.replace(/(\d{4})/g, '$1 ').trim(); input.value = v; }
    function formatExpiry(input) { let v = input.value.replace(/\D/g, '').slice(0,4); if(v.length >= 2) v=v.slice(0,2)+'/'+v.slice(2); input.value = v; }
    function formatCPF(input) { let v = input.value.replace(/\D/g, '').slice(0,11); if(v.length > 9) v=v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4'); else if(v.length > 6) v=v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3'); else if(v.length > 3) v=v.replace(/(\d{3})(\d{1,3})/, '$1.$2'); input.value = v; }

</script>

<?php include '../views/layout/footer.php'; ?>