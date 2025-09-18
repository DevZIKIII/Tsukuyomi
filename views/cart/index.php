<?php include '../views/layout/header.php'; ?>

<h1>🛒 Meu Carrinho</h1>

<div class="cart-container fade-in">
    <?php if(!empty($cart_items)): ?>
        <div class="cart-items">
            <?php foreach($cart_items as $index => $item): ?>
                <div class="cart-item slide-in-left" id="cart-item-<?php echo $item['id']; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                    <img src="images/products/<?php echo $item['image_url']; ?>" 
                         alt="<?php echo $item['name']; ?>" 
                         class="cart-item-image"
                         onerror="this.src='images/placeholder.jpg'"
                         loading="lazy">
                    
                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>📏 Tamanho: <strong><?php echo $item['size']; ?></strong></p>
                        <p class="product-price">💰 R$ <span class="item-price"><?php echo number_format($item['price'], 2, ',', '.'); ?></span></p>
                    </div>
                    
                    <div class="cart-item-actions">
                        <div class="quantity-controls">
                            <button class="quantity-btn minus" onclick="changeQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                            <input type="number" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1" 
                                   max="<?php echo $item['stock_quantity']; ?>"
                                   class="quantity-input"
                                   id="quantity-<?php echo $item['id']; ?>"
                                   data-cart-id="<?php echo $item['id']; ?>"
                                   data-price="<?php echo $item['price']; ?>"
                                   onchange="updateCartItem(<?php echo $item['id']; ?>, this.value)">
                            <button class="quantity-btn plus" onclick="changeQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                        </div>
                        
                        <p class="item-total">
                            💳 Total: R$ <span id="item-total-<?php echo $item['id']; ?>"><?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
                        </p>
                        
                        <a href="index.php?action=remove_from_cart&id=<?php echo $item['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Remover este item do carrinho?')">🗑️ Remover</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary slide-in-right">
            <h3>📋 Resumo do Pedido</h3>
            
            <!-- Cupom de Desconto -->
            <div class="coupon-section">
                <h4>🎫 Cupom de Desconto</h4>
                <?php if(isset($_SESSION['coupon']) && $_SESSION['coupon']['valid']): ?>
                    <div class="applied-coupon">
                        <div class="coupon-info">
                            <span class="coupon-code"><?php echo $_SESSION['coupon']['code']; ?></span>
                            <span class="coupon-desc"><?php echo $_SESSION['coupon']['description']; ?></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCoupon()">Remover</button>
                    </div>
                <?php else: ?>
                    <div class="coupon-input-group">
                        <input type="text" 
                               id="coupon_code" 
                               class="form-control" 
                               placeholder="🎟️ Digite o código do cupom"
                               style="text-transform: uppercase;">
                        <button type="button" class="btn btn-secondary" onclick="applyCoupon()">Aplicar</button>
                    </div>
                    <div id="coupon-message" class="coupon-message"></div>
                <?php endif; ?>
            </div>
            
            <div class="summary-line">
                <span>Subtotal:</span>
                <span id="cart-subtotal">💰 R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
            </div>
            
            <?php if(isset($_SESSION['coupon']) && $_SESSION['coupon']['valid']): ?>
                <div class="summary-line discount-line">
                    <span>🎉 Desconto:</span>
                    <span id="cart-discount" class="discount-value">-💰 R$ <?php echo number_format($_SESSION['coupon']['discount_amount'], 2, ',', '.'); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="summary-line">
                <span>🚚 Frete:</span>
                <span id="cart-shipping">✅ Grátis</span>
            </div>
            <hr>
            <div class="cart-total">
                <span>💳 Total:</span>
                <?php 
                $final_total = $total;
                if(isset($_SESSION['coupon']) && $_SESSION['coupon']['valid']) {
                    $final_total = $total - $_SESSION['coupon']['discount_amount'];
                }
                ?>
                <span id="cart-total">💰 R$ <?php echo number_format($final_total, 2, ',', '.'); ?></span>
            </div>
            
            <form action="index.php?action=create_order" method="POST" class="checkout-form" onsubmit="return validateCheckout()">
                <!-- Seleção de Forma de Pagamento -->
                <div class="form-group">
                    <label>💳 Forma de Pagamento</label>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card" onclick="showPaymentForm('card')">
                            <span class="payment-icon">💳</span>
                            <span>Cartão</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="pix" onclick="showPaymentForm('pix')">
                            <span class="payment-icon">📱</span>
                            <span>PIX</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="boleto" onclick="showPaymentForm('boleto')">
                            <span class="payment-icon">📄</span>
                            <span>Boleto</span>
                        </label>
                    </div>
                </div>
                
                <!-- Formulário de Cartão -->
                <div id="card-form" class="payment-form" style="display: none;">
                    <h4>💳 Dados do Cartão</h4>
                    
                    <div class="form-group">
                        <label>Tipo de Cartão</label>
                        <div class="card-type-selector">
                            <label class="radio-option">
                                <input type="radio" name="card_type" value="credit" checked>
                                <span>Crédito</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="card_type" value="debit">
                                <span>Débito</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_number">Número do Cartão</label>
                        <input type="text" 
                               id="card_number" 
                               name="card_number" 
                               class="form-control" 
                               placeholder="0000 0000 0000 0000"
                               maxlength="19"
                               oninput="formatCardNumber(this)">
                    </div>
                    
                    <div class="form-group">
                        <label for="card_name">Nome no Cartão</label>
                        <input type="text" 
                               id="card_name" 
                               name="card_name" 
                               class="form-control" 
                               placeholder="NOME COMO ESTÁ NO CARTÃO"
                               style="text-transform: uppercase;">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="card_expiry">Validade</label>
                            <input type="text" 
                                   id="card_expiry" 
                                   name="card_expiry" 
                                   class="form-control" 
                                   placeholder="MM/AA"
                                   maxlength="5"
                                   oninput="formatExpiry(this)">
                        </div>
                        
                        <div class="form-group half">
                            <label for="card_cvv">CVV</label>
                            <input type="text" 
                                   id="card_cvv" 
                                   name="card_cvv" 
                                   class="form-control" 
                                   placeholder="123"
                                   maxlength="4"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_cpf">CPF do Titular</label>
                        <input type="text" 
                               id="card_cpf" 
                               name="card_cpf" 
                               class="form-control" 
                               placeholder="000.000.000-00"
                               maxlength="14"
                               oninput="formatCPF(this)">
                    </div>
                </div>
                
                <!-- Formulário PIX -->
                <div id="pix-form" class="payment-form" style="display: none;">
                    <h4>📱 Pagamento via PIX</h4>
                    <div class="pix-info">
                        <p>📱 Ao finalizar o pedido, você receberá um QR Code para pagamento.</p>
                        <p>⏰ O prazo para pagamento é de 30 minutos.</p>
                        <div class="pix-benefits">
                            <span>✓ Pagamento instantâneo</span>
                            <span>✓ Sem taxas adicionais</span>
                            <span>✓ 100% seguro</span>
                        </div>
                    </div>
                </div>
                
                <!-- Formulário Boleto -->
                <div id="boleto-form" class="payment-form" style="display: none;">
                    <h4>📄 Pagamento via Boleto</h4>
                    <div class="boleto-info">
                        <p>📄 O boleto será gerado após a confirmação do pedido.</p>
                        <p>⏰ Prazo de pagamento: 3 dias úteis.</p>
                        <div class="form-group">
                            <label for="boleto_cpf">CPF para emissão do boleto</label>
                            <input type="text" 
                                   id="boleto_cpf" 
                                   name="boleto_cpf" 
                                   class="form-control" 
                                   placeholder="000.000.000-00"
                                   maxlength="14"
                                   oninput="formatCPF(this)">
                        </div>
                    </div>
                </div>
                
                <!-- Endereço de Entrega -->
                <div class="shipping-section">
                    <h4>🏠 Endereço de Entrega</h4>
                    
                    <div class="form-group">
                        <label for="shipping_cep">📮 CEP</label>
                        <div class="cep-input-group">
                            <input type="text" 
                                   id="shipping_cep" 
                                   name="shipping_cep" 
                                   class="form-control" 
                                   placeholder="00000-000"
                                   maxlength="9"
                                   oninput="formatCEP(this)"
                                   onblur="buscarCEP(this.value)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="buscarCEPClick()">
                                Buscar CEP
                            </button>
                        </div>
                        <small class="form-text">
                            <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank">
                                🔍 Não sei meu CEP
                            </a>
                        </small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-8">
                            <label for="shipping_street">Rua/Avenida</label>
                            <input type="text" 
                                   id="shipping_street" 
                                   name="shipping_street" 
                                   class="form-control" 
                                   placeholder="Ex: Rua das Flores"
                                   required>
                        </div>
                        
                        <div class="form-group col-4">
                            <label for="shipping_number">Número</label>
                            <input type="text" 
                                   id="shipping_number" 
                                   name="shipping_number" 
                                   class="form-control" 
                                   placeholder="123"
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_complement">Complemento</label>
                        <input type="text" 
                               id="shipping_complement" 
                               name="shipping_complement" 
                               class="form-control" 
                               placeholder="Apartamento, bloco, casa, etc. (opcional)">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-5">
                            <label for="shipping_neighborhood">Bairro</label>
                            <input type="text" 
                                   id="shipping_neighborhood" 
                                   name="shipping_neighborhood" 
                                   class="form-control" 
                                   placeholder="Ex: Centro"
                                   required>
                        </div>
                        
                        <div class="form-group col-5">
                            <label for="shipping_city">Cidade</label>
                            <input type="text" 
                                   id="shipping_city" 
                                   name="shipping_city" 
                                   class="form-control" 
                                   placeholder="Ex: São Paulo"
                                   required>
                        </div>
                        
                        <div class="form-group col-2">
                            <label for="shipping_state">Estado</label>
                            <select id="shipping_state" 
                                    name="shipping_state" 
                                    class="form-control" 
                                    required>
                                <option value="">UF</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_reference">Ponto de Referência</label>
                        <input type="text" 
                               id="shipping_reference" 
                               name="shipping_reference" 
                               class="form-control" 
                               placeholder="Ex: Próximo ao supermercado (opcional)">
                    </div>
                    
                    <!-- Opção de salvar endereço -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="save_address" 
                               name="save_address" 
                               value="1">
                        <label class="form-check-label" for="save_address">
                            💾 Salvar este endereço para próximas compras
                        </label>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Campo hidden para endereço completo formatado -->
                    <input type="hidden" name="shipping_address" id="shipping_address_formatted">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">🎉 Finalizar Pedido</button>
            </form>
            
            <div class="cart-actions">
                <a href="index.php?action=products" class="btn btn-secondary">🛍️ Continuar Comprando</a>
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

<style>
/* Estilos do sistema de pagamento */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.payment-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option:hover {
    border-color: var(--primary-color);
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option input[type="radio"]:checked + .payment-icon {
    color: var(--primary-color);
}

.payment-option input[type="radio"]:checked ~ span {
    color: var(--primary-color);
}

.payment-option input[type="radio"]:checked + .payment-icon + span {
    font-weight: 600;
}

.payment-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.payment-form {
    background-color: var(--surface-color);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.payment-form h4 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

/* Estilos do formulário de endereço */
.shipping-section {
    background-color: var(--surface-color);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.shipping-section h4 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.cep-input-group {
    display: flex;
    gap: 0.5rem;
}

.cep-input-group input {
    flex: 1;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.form-text a {
    color: var(--primary-color);
    text-decoration: underline;
}

.form-row {
    display: grid;
    gap: 1rem;
    margin-bottom: 1rem;
}

.col-2 { grid-column: span 2; }
.col-4 { grid-column: span 4; }
.col-5 { grid-column: span 5; }
.col-8 { grid-column: span 8; }

@media (min-width: 768px) {
    .form-row {
        grid-template-columns: repeat(12, 1fr);
    }
}

.form-check {
    margin-top: 1rem;
}

.form-check-input {
    margin-right: 0.5rem;
}

/* Indicador de carregamento do CEP */
.cep-loading {
    display: none;
    color: var(--primary-color);
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.cep-loading.active {
    display: block;
}

/* Outros estilos já existentes... */

.card-type-selector {
    display: flex;
    gap: 1rem;
}

.radio-option {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.radio-option input[type="radio"] {
    margin-right: 0.5rem;
}

.radio-option:hover {
    border-color: var(--primary-color);
}

.radio-option input[type="radio"]:checked + span {
    color: var(--primary-color);
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group.half {
    margin-bottom: 0;
}

.pix-info, .boleto-info {
    background-color: var(--background-color);
    padding: 1rem;
    border-radius: 0.5rem;
}

.pix-benefits {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
    color: var(--primary-color);
}

/* Outros estilos do carrinho */
.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border: 1px solid var(--border-color);
    background-color: var(--surface-color);
    color: var(--text-primary);
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.quantity-btn:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

@keyframes highlight {
    0% { background-color: rgba(139, 92, 246, 0.2); }
    100% { background-color: transparent; }
}

.updated {
    animation: highlight 0.5s ease-out;
}

@media (max-width: 768px) {
    .payment-methods {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>

// Aplicar cupom
function applyCoupon() {
    const code = document.getElementById('coupon_code').value.trim();
    const messageDiv = document.getElementById('coupon-message');
    
    if (!code) {
        messageDiv.textContent = 'Por favor, digite um código de cupom';
        messageDiv.className = 'coupon-message error';
        return;
    }
    
    const subtotalText = document.getElementById('cart-subtotal').textContent;
    const subtotal = parseFloat(subtotalText.replace('R$ ', '').replace('.', '').replace(',', '.'));
    
    const formData = new FormData();
    formData.append('code', code);
    formData.append('total', subtotal);
    
    fetch('index.php?action=validate_coupon', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            messageDiv.textContent = data.message;
            messageDiv.className = 'coupon-message success';
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.textContent = data.message;
            messageDiv.className = 'coupon-message error';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        messageDiv.textContent = 'Erro ao validar cupom';
        messageDiv.className = 'coupon-message error';
    });
}

// Remover cupom
function removeCoupon() {
    if (confirm('Deseja remover o cupom de desconto?')) {
        fetch('index.php?action=remove_coupon', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    }
}

// Função para buscar CEP ao clicar no botão
function buscarCEPClick() {
    const cepInput = document.getElementById('shipping_cep');
    if (cepInput) {
        buscarCEP(cepInput.value);
    }
}

// Formatar CEP
function formatCEP(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length > 5) {
        value = value.substring(0, 5) + '-' + value.substring(5, 8);
    }
    
    input.value = value;
}

// Buscar CEP
function buscarCEP(cep) {
    console.log('Buscando CEP:', cep);
    
    // Remove caracteres não numéricos
    cep = cep.replace(/\D/g, '');
    
    // Verifica se o CEP tem 8 dígitos
    if (cep.length !== 8) {
        alert('Por favor, digite um CEP válido com 8 dígitos');
        return;
    }
    
    // Remove loading anterior se existir
    const existingLoading = document.querySelector('.cep-loading');
    if (existingLoading) {
        existingLoading.remove();
    }
    
    // Mostra indicador de carregamento
    const cepContainer = document.getElementById('shipping_cep').parentElement;
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'cep-loading active';
    loadingDiv.innerHTML = '🔄 Buscando endereço...';
    loadingDiv.style.color = '#8b5cf6';
    loadingDiv.style.fontSize = '0.875rem';
    loadingDiv.style.marginTop = '0.5rem';
    cepContainer.appendChild(loadingDiv);
    
    // Desabilita campos durante a busca
    const fields = ['shipping_street', 'shipping_neighborhood', 'shipping_city', 'shipping_state'];
    fields.forEach(id => {
        const field = document.getElementById(id);
        if (field) field.disabled = true;
    });
    
    // Faz a requisição para a API do ViaCEP
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            
            if (!data.erro) {
                // Preenche os campos com os dados retornados
                if (data.logradouro) {
                    document.getElementById('shipping_street').value = data.logradouro;
                }
                if (data.bairro) {
                    document.getElementById('shipping_neighborhood').value = data.bairro;
                }
                if (data.localidade) {
                    document.getElementById('shipping_city').value = data.localidade;
                }
                if (data.uf) {
                    document.getElementById('shipping_state').value = data.uf;
                }
                
                // Foca no campo número
                document.getElementById('shipping_number').focus();
                
                // Mostra mensagem de sucesso
                loadingDiv.innerHTML = '✅ Endereço encontrado!';
                loadingDiv.style.color = '#22c55e';
                setTimeout(() => loadingDiv.remove(), 3000);
            } else {
                loadingDiv.innerHTML = '❌ CEP não encontrado';
                loadingDiv.style.color = '#ef4444';
                setTimeout(() => loadingDiv.remove(), 3000);
            }
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            loadingDiv.innerHTML = '❌ Erro ao buscar CEP';
            loadingDiv.style.color = '#ef4444';
            setTimeout(() => loadingDiv.remove(), 3000);
        })
        .finally(() => {
            // Habilita campos novamente
            fields.forEach(id => {
                const field = document.getElementById(id);
                if (field) field.disabled = false;
            });
        });
}

// Mostrar formulário de pagamento
function showPaymentForm(method) {
    // Esconder todos os formulários
    document.querySelectorAll('.payment-form').forEach(form => {
        form.style.display = 'none';
    });
    
    // Mostrar o formulário selecionado
    const formId = method + '-form';
    const form = document.getElementById(formId);
    if (form) {
        form.style.display = 'block';
    }
    
    // Atualizar campos obrigatórios
    updateRequiredFields(method);
}

// Atualizar campos obrigatórios baseado no método de pagamento
function updateRequiredFields(method) {
    // Remover required de todos os campos de pagamento
    document.querySelectorAll('.payment-form input').forEach(input => {
        input.removeAttribute('required');
    });
    
    // Adicionar required aos campos do método selecionado
    if (method === 'card') {
        document.getElementById('card_number').setAttribute('required', 'required');
        document.getElementById('card_name').setAttribute('required', 'required');
        document.getElementById('card_expiry').setAttribute('required', 'required');
        document.getElementById('card_cvv').setAttribute('required', 'required');
        document.getElementById('card_cpf').setAttribute('required', 'required');
    } else if (method === 'boleto') {
        document.getElementById('boleto_cpf').setAttribute('required', 'required');
    }
}

// Formatar número do cartão
function formatCardNumber(input) {
    let value = input.value.replace(/\s/g, '');
    let formattedValue = '';
    
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    
    input.value = formattedValue;
}

// Formatar data de validade
function formatExpiry(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    
    input.value = value;
}

// Formatar CPF
function formatCPF(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = value;
}

// Formatar endereço completo antes de enviar
function formatFullAddress() {
    const street = document.getElementById('shipping_street').value;
    const number = document.getElementById('shipping_number').value;
    const complement = document.getElementById('shipping_complement').value;
    const neighborhood = document.getElementById('shipping_neighborhood').value;
    const city = document.getElementById('shipping_city').value;
    const state = document.getElementById('shipping_state').value;
    const cep = document.getElementById('shipping_cep').value;
    const reference = document.getElementById('shipping_reference').value;
    
    let fullAddress = `${street}, ${number}`;
    
    if (complement) {
        fullAddress += ` - ${complement}`;
    }
    
    fullAddress += `, ${neighborhood}, ${city} - ${state}, CEP: ${cep}`;
    
    if (reference) {
        fullAddress += ` (Ref: ${reference})`;
    }
    
    document.getElementById('shipping_address_formatted').value = fullAddress;
}

// Validar checkout atualizado
function validateCheckout() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!paymentMethod) {
        alert('Por favor, selecione uma forma de pagamento');
        return false;
    }
    
    // Formatar endereço completo antes de enviar
    formatFullAddress();
    
    return true;
}

// Função para mudar quantidade com botões + e -
function changeQuantity(cartId, change) {
    const input = document.getElementById('quantity-' + cartId);
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.max);
    const newValue = currentValue + change;
    
    if (newValue >= 1 && newValue <= maxValue) {
        input.value = newValue;
        updateCartItem(cartId, newValue);
    }
}

// Função para atualizar item do carrinho
function updateCartItem(cartId, quantity) {
    const cartItem = document.getElementById('cart-item-' + cartId);
    cartItem.classList.add('loading');
    
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    
    fetch('index.php?action=update_cart', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Resposta:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                // Atualizar o total do item
                const input = document.getElementById('quantity-' + cartId);
                const price = parseFloat(input.dataset.price);
                const itemTotal = price * quantity;
                
                document.getElementById('item-total-' + cartId).textContent = 
                    itemTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                
                updateCartTotal();
                
                cartItem.classList.remove('loading');
                cartItem.classList.add('updated');
                setTimeout(() => cartItem.classList.remove('updated'), 500);
            } else {
                console.error('Erro:', data.message);
                location.reload();
            }
        } catch (e) {
            console.error('Erro ao processar resposta:', e);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        location.reload();
    });
}

// Recalcular total
function updateCartTotal() {
    let total = 0;
    
    document.querySelectorAll('.cart-item').forEach(item => {
        const itemId = item.id.replace('cart-item-', '');
        const itemTotalText = document.getElementById('item-total-' + itemId).textContent;
        const itemTotal = parseFloat(itemTotalText.replace('.', '').replace(',', '.'));
        total += itemTotal;
    });
    
    document.getElementById('cart-subtotal').textContent = 
        'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('cart-total').textContent = 
        'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Adicionar evento para formatar endereço ao mudar campos
document.addEventListener('DOMContentLoaded', function() {
    const addressFields = [
        'shipping_street', 'shipping_number', 'shipping_complement',
        'shipping_neighborhood', 'shipping_city', 'shipping_state', 
        'shipping_cep', 'shipping_reference'
    ];
    
    addressFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', formatFullAddress);
        }
    });
    
    // Adicionar evento para buscar CEP ao pressionar Enter
    const cepInput = document.getElementById('shipping_cep');
    if (cepInput) {
        cepInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarCEP(this.value);
            }
        });
    }
});
</script>

<?php include '../views/layout/footer.php'; ?>