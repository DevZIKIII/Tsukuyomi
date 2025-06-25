<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Editar Produto</h2>
    
    <form action="/tsukuyomi/public/index.php?action=update_product&id=<?php echo $this->product->id; ?>" method="POST">
        <div class="form-group">
            <label for="name">Nome do Produto *</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($this->product->name); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($this->product->description); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Preço (R$) *</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" 
                   value="<?php echo $this->product->price; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="category">Categoria *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <option value="Camisetas" <?php echo $this->product->category == 'Camisetas' ? 'selected' : ''; ?>>Camisetas</option>
                <option value="Moletons" <?php echo $this->product->category == 'Moletons' ? 'selected' : ''; ?>>Moletons</option>
                <option value="Jaquetas" <?php echo $this->product->category == 'Jaquetas' ? 'selected' : ''; ?>>Jaquetas</option>
                <option value="Calças" <?php echo $this->product->category == 'Calças' ? 'selected' : ''; ?>>Calças</option>
                <option value="Shorts" <?php echo $this->product->category == 'Shorts' ? 'selected' : ''; ?>>Shorts</option>
                <option value="Acessórios" <?php echo $this->product->category == 'Acessórios' ? 'selected' : ''; ?>>Acessórios</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="size">Tamanho *</label>
            <select id="size" name="size" class="form-control" required>
                <option value="">Selecione um tamanho</option>
                <option value="PP" <?php echo $this->product->size == 'PP' ? 'selected' : ''; ?>>PP</option>
                <option value="P" <?php echo $this->product->size == 'P' ? 'selected' : ''; ?>>P</option>
                <option value="M" <?php echo $this->product->size == 'M' ? 'selected' : ''; ?>>M</option>
                <option value="G" <?php echo $this->product->size == 'G' ? 'selected' : ''; ?>>G</option>
                <option value="GG" <?php echo $this->product->size == 'GG' ? 'selected' : ''; ?>>GG</option>
                <option value="XG" <?php echo $this->product->size == 'XG' ? 'selected' : ''; ?>>XG</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="stock_quantity">Quantidade em Estoque *</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" 
                   value="<?php echo $this->product->stock_quantity; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="image_url">Nome do Arquivo da Imagem</label>
            <input type="text" id="image_url" name="image_url" class="form-control" 
                   value="<?php echo htmlspecialchars($this->product->image_url); ?>"
                   placeholder="exemplo: naruto_akatsuki.jpg">
            <small>Faça upload da imagem para a pasta /public/images/products/</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Produto</button>
        <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>