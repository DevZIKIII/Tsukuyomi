// Tsukuyomi Streetwear - JavaScript

// Enhanced loading states and animations
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to page
    document.body.classList.add('loaded');
    
    // Initialize intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe all animatable elements
    document.querySelectorAll('.product-card, .order-card, .cart-item').forEach(el => {
        observer.observe(el);
    });
});

// Add to cart function
function addToCart(productId) {
    console.log('Adicionando produto ID:', productId);
    
    // Add loading state to button
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '⏳ Adicionando...';
    button.disabled = true;
    
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
                // Show success animation
                button.innerHTML = '✅ Adicionado!';
                button.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
                
                // Show toast notification
                showToast('🎉 Produto adicionado ao carrinho!', 'success');
                
                // Recarregar a página para atualizar o contador
                setTimeout(() => location.reload(), 1000);
            } else {
                button.innerHTML = originalText;
                button.disabled = false;
                showToast(data.message || 'Erro ao adicionar ao carrinho', 'error');
            }
        } catch (e) {
            // Se não for JSON válido, mostrar o texto recebido
            console.error('Erro ao parsear JSON:', e);
            console.error('Texto recebido:', text);
            
            // Verificar se é erro de login
            if (text.includes('login')) {
                showToast('🔐 Você precisa fazer login primeiro!', 'warning');
                window.location.href = 'index.php?action=login';
            } else {
                button.innerHTML = originalText;
                button.disabled = false;
                showToast('❌ Erro ao processar resposta do servidor', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        showToast('❌ Erro ao adicionar ao carrinho. Verifique se você está logado.', 'error');
    });
}

// Toast notification system
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add toast styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        color: white;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
        max-width: 400px;
        word-wrap: break-word;
    `;
    
    // Set background based on type
    const backgrounds = {
        success: 'linear-gradient(135deg, #22c55e, #16a34a)',
        error: 'linear-gradient(135deg, #ef4444, #dc2626)',
        warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
        info: 'linear-gradient(135deg, #8b5cf6, #7c3aed)'
    };
    
    toast.style.background = backgrounds[type] || backgrounds.info;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
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
            showToast('✅ Quantidade atualizada!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast('❌ Erro ao atualizar quantidade', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('❌ Erro de conexão', 'error');
    });
}

// Enhanced mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            
            // Animate hamburger icon
            if (navLinks.classList.contains('active')) {
                menuToggle.innerHTML = '✕';
                menuToggle.style.transform = 'rotate(180deg)';
            } else {
                menuToggle.innerHTML = '☰';
                menuToggle.style.transform = 'rotate(0deg)';
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!menuToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('active');
                menuToggle.innerHTML = '☰';
                menuToggle.style.transform = 'rotate(0deg)';
            }
        });
    }
});

// Enhanced form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    let firstInvalidInput = null;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            if (!firstInvalidInput) firstInvalidInput = input;
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showToast('⚠️ Por favor, preencha todos os campos obrigatórios', 'warning');
        if (firstInvalidInput) {
            firstInvalidInput.focus();
            firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

// Enhanced input interactions
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        // Remove error class on input
        input.addEventListener('input', function() {
            this.classList.remove('error');
        });
        
        // Add focus animations
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});

// Enhanced alert system
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'alert-close';
        closeBtn.style.cssText = `
            position: absolute;
            top: 0.5rem;
            right: 0.75rem;
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        `;
        closeBtn.addEventListener('click', () => {
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 300);
        });
        closeBtn.addEventListener('mouseenter', () => closeBtn.style.opacity = '1');
        closeBtn.addEventListener('mouseleave', () => closeBtn.style.opacity = '0.7');
        
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);
        
        // Auto-hide after 7 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.transition = 'transform 0.5s ease-out, opacity 0.5s ease-out';
                alert.style.transform = 'translateX(100%)';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 7000);
    });
});

// Enhanced scroll animations
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const header = document.querySelector('header');
    
    if (scrolled > 100) {
        header.style.background = 'rgba(26, 26, 26, 0.98)';
        header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
    } else {
        header.style.background = 'rgba(26, 26, 26, 0.95)';
        header.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
    }
});

// Add smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const allForms = document.querySelectorAll('form');

    allForms.forEach(form => {
        form.addEventListener('submit', function() {
            // Quando o formulário começa a ser enviado,
            // encontramos o botão de submit dentro dele.
            const submitButton = form.querySelector('button[type="submit"]');
            
            if (submitButton) {
                // Agora é seguro desabilitar o botão e mudar o texto,
                // pois o envio do formulário já foi iniciado.
                submitButton.disabled = true;
                submitButton.innerHTML = '⏳ Processando...';
            }
        });
    });
});

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    // ESC key closes modals and menus
    if (e.key === 'Escape') {
        const activeMenu = document.querySelector('.nav-links.active');
        if (activeMenu) {
            activeMenu.classList.remove('active');
            const menuToggle = document.querySelector('.menu-toggle');
            if (menuToggle) {
                menuToggle.innerHTML = '☰';
                menuToggle.style.transform = 'rotate(0deg)';
            }
        }
        
        // Close any open toasts
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
    }
});

// Add performance monitoring
window.addEventListener('load', function() {
    // Log page load time
    const loadTime = performance.now();
    console.log(`🚀 Página carregada em ${Math.round(loadTime)}ms`);
    
    // Add loaded class for CSS animations
    document.body.classList.add('page-loaded');
});