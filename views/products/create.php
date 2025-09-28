<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Adicionar Novo Produto</h2>
    <form action="/tsukuyomi/public/index.php?action=store_product" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nome do Produto *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Ex: Camiseta Akatsuki" required>
            <small>O sistema criará variações para todos os tamanhos (PP-XG).</small>
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
                <option value="">Selecione</option>
                <option value="Camisetas">Camisetas</option>
                <option value="Moletons">Moletons</option>
                <option value="Jaquetas">Jaquetas</option>
                <option value="Calças">Calças</option>
                <option value="Shorts">Shorts</option>
                <option value="Acessórios">Acessórios</option>
            </select>
        </div>
        <div class="form-group">
            <label for="stock_quantity">Estoque Inicial (para cada tamanho) *</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="image_file">Imagem do Produto *</label>
            <input type="file" id="image_file" name="image_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar Produto</button>
        <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>