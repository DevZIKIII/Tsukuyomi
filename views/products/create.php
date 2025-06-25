<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Adicionar Novo Produto</h2>
    
    <form action="/tsukuyomi/public/index.php?action=store_product" method="POST">
        <div class="form-group">
            <label for="name">Nome do Produto *</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Preço (R$) *</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="category">Categoria *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <option value="Camisetas">Camisetas</option>
                <option value="Moletons">Moletons</option>
                <option value="Jaquetas">Jaquetas</option>
                <option value="Calças">Calças</option>
                <option value="Shorts">Shorts</option>
                <option value="Acessórios">Acessórios</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="size">Tamanho *</label>
            <select id="size" name="size" class="form-control" required>
                <option value="">Selecione um tamanho</option>
                <option value="PP">PP</option>
                <option value="P">P</option>
                <option value="M">M</option>
                <option value="G">G</option>
                <option value="GG">GG</option>
                <option value="XG">XG</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="stock_quantity">Quantidade em Estoque *</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="image_url">Nome do Arquivo da Imagem</label>
            <input type="text" id="image_url" name="image_url" class="form-control" 
                   placeholder="exemplo: naruto_akatsuki.jpg">
            <small>Faça upload da imagem para a pasta /public/images/products/</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Adicionar Produto</button>
        <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>