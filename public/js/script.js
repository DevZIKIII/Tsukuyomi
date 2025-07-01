// Tsukuyomi Streetwear - JavaScript

// Add to cart function
function addToCart(productId) {
    console.log('Adicionando produto ID:', productId);
    
    // Criar FormData para enviar via POST
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    
    // Fazer a requisição AJAX
    fetch('index.php?action=add_to_cart', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status da resposta:', response.status);
        return response.text(); // Primeiro pegar como texto
    })
    .then(text => {
        console.log('Resposta do servidor:', text);
        
        try {
            // Tentar fazer parse do JSON
            const data = JSON.parse(text);
            console.log('Dados parseados:', data);
            
            if (data.success) {
                alert('Produto adicionado ao carrinho!');
                // Recarregar a página para atualizar o contador
                location.reload();
            } else {
                alert(data.message || 'Erro ao adicionar ao carrinho');
            }
        } catch (e) {
            // Se não for JSON válido, mostrar o texto recebido
            console.error('Erro ao parsear JSON:', e);
            console.error('Texto recebido:', text);
            
            // Verificar se é erro de login
            if (text.includes('login')) {
                alert('Você precisa fazer login primeiro!');
                window.location.href = 'index.php?action=login';
            } else {
                alert('Erro ao processar resposta do servidor. Verifique o console.');
            }
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro ao adicionar ao carrinho. Verifique se você está logado.');
    });
}

// Update cart count
function updateCartCount() {
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        let count = parseInt(cartBadge.textContent) || 0;
        cartBadge.textContent = count + 1;
    }
}

// Update cart quantity
function updateCartQuantity(cartId, quantity) {
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    
    fetch('index.php?action=update_cart', {
        method: 'POST',
        body: formData
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

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });
    
    if (!isValid) {
        alert('Por favor, preencha todos os campos obrigatórios');
    }
    
    return isValid;
}

// Remove error class on input
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
        });
    });
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});