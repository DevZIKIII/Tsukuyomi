<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Editar Produto</h2>
    <form action="/tsukuyomi/public/index.php?action=update_product&id=<?php echo $product->id; ?>" method="POST" enctype="multipart/form-data">
        
        <h4>Informações Principais</h4>
        <div class="form-group">
            <label for="name">Nome do Produto *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product->description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Preço (R$) *</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo $product->price; ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Categoria *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="Camisetas" <?php echo $product->category == 'Camisetas' ? 'selected' : ''; ?>>Camisetas</option>
                <option value="Moletons" <?php echo $product->category == 'Moletons' ? 'selected' : ''; ?>>Moletons</option>
                <option value="Jaquetas" <?php echo $product->category == 'Jaquetas' ? 'selected' : ''; ?>>Jaquetas</option>
            </select>
        </div>
        <div class="form-group">
            <label for="image_file">Alterar Imagem</label>
            <input type="file" id="image_file" name="image_file" class="form-control">
            <small>Atual: <?php echo htmlspecialchars($product->image_url); ?></small>
            <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($product->image_url); ?>">
        </div>

        <hr style="margin: 2rem 0; border-color: var(--border-color);">

        <h4>Gerenciar Estoque por Tamanho</h4>
        <div class="stock-management-grid">
            <?php foreach($product->variants as $variant): ?>
                <div class="form-group">
                    <label>Estoque (<?php echo $variant['size']; ?>)</label>
                    <input type="number" name="stock[<?php echo $variant['id']; ?>]" class="form-control" value="<?php echo $variant['stock_quantity']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar Produto</button>
        <a href="/tsukuyomi/public/index.php?action=products" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<style>.stock-management-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; margin-bottom: 2rem;}</style>
<?php include '../views/layout/footer.php'; ?>