<?php include '../views/layout/header.php'; ?>

<div class="form-container">
    <h2>Editar Opção de Voto</h2>
    <form action="index.php?action=update_vote&id=<?php echo htmlspecialchars($vote_option['id']); ?>" method="POST">
        <div class="form-group">
            <label for="name">Nome da Coleção (Anime) *</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?php echo htmlspecialchars($vote_option['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="image_url">Imagem de Exibição *</label>
            <select id="image_url" name="image_url" class="form-control" required>
                <option value="">Selecione uma imagem da pasta de produtos</option>
                <?php foreach ($product_images as $image): ?>
                    <option value="<?php echo htmlspecialchars($image); ?>" 
                            <?php echo ($image == $vote_option['image_url']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($image); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>A lista é gerada a partir da pasta `public/images/products/`.</small>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php?action=admin_votes" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../views/layout/footer.php'; ?>