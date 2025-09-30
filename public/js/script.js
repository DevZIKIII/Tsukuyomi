document.addEventListener('DOMContentLoaded', function() {
    // --- LÓGICA DO MENU MOBILE (HAMBURGER) ---
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenuContainer = document.querySelector('.mobile-menu-container');

    if (menuToggle && mobileMenuContainer) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenuContainer.classList.toggle('active');

            if (mobileMenuContainer.classList.contains('active')) {
                menuToggle.innerHTML = '✕';
                document.body.style.overflow = 'hidden';
            } else {
                menuToggle.innerHTML = '☰';
                document.body.style.overflow = '';
            }
        });
    }

    // --- FECHAR MENU MOBILE AO CLICAR FORA ---
    document.addEventListener('click', (e) => {
        if (mobileMenuContainer && mobileMenuContainer.classList.contains('active')) {
            if (!mobileMenuContainer.contains(e.target) && !menuToggle.contains(e.target)) {
                mobileMenuContainer.classList.remove('active');
                menuToggle.innerHTML = '☰';
                document.body.style.overflow = '';
            }
        }
    });

    // --- ALERTA DE SUCESSO/ERRO ---
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // --- DESABILITAR BOTÃO DE SUBMIT APÓS CLIQUE ---
    const allForms = document.querySelectorAll('form');
    allForms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.hasAttribute('data-no-disable')) {
                submitButton.disabled = true;
                submitButton.innerHTML = '⏳ Processando...';
            }
        });
    });
});