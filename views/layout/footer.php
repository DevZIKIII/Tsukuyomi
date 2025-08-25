<?php 
// views/layout/footer.php
?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Sobre a Tsukuyomi</h4>
                    <p>A melhor loja de streetwear geek do Brasil. Roupas exclusivas para quem vive a cultura otaku.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="<?php echo isset($config) && function_exists('url') ? url() : 'index.php'; ?>">Home</a></li>
                        <li><a href="<?php echo isset($config) && function_exists('url') ? url('products') : 'index.php?action=products'; ?>">Produtos</a></li>
                        <li><a href="<?php echo isset($config) && function_exists('url') ? url('orders') : 'index.php?action=orders'; ?>">Meus Pedidos</a></li>
                        <li><a href="<?php echo isset($config) && function_exists('url') ? url('profile') : 'index.php?action=profile'; ?>">Minha Conta</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contato</h4>
                    <ul>
                        <li>Email: contato@tsukuyomi.com</li>
                        <li>WhatsApp: (11) 99999-9999</li>
                        <li>Seg-Sex: 9h às 18h</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Formas de Pagamento</h4>
                    <ul>
                        <li>Cartão de Crédito</li>
                        <li>PIX</li>
                        <li>Boleto</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Tsukuyomi. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

<script src="<?php echo defined('BASE_URL') ? BASE_URL . 'js/script.js' : 'js/script.js'; ?>"></script>
</body>
</html>