<?php include '../views/layout/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Gerenciar Votações</h1>
        <div>
            <a href="index.php?action=create_vote" class="btn btn-primary">Adicionar Nova Opção</a>
            <a href="index.php?action=reset_votes" class="btn btn-danger" onclick="return confirm('ATENÇÃO: Isso irá zerar TODOS os votos e limpar o registro de quem votou. Deseja continuar?')">Resetar Votação</a>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome da Coleção</th>
                    <th>Votos</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($vote_options as $option): ?>
                    <tr>
                        <td>
                            <img src="images/products/<?php echo htmlspecialchars($option['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($option['name']); ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($option['name']); ?></strong></td>
                        <td><?php echo $option['votes']; ?></td>
                        
                        <td>
                            <?php if($option['is_active']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="index.php?action=edit_vote&id=<?php echo $option['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                            <a href="index.php?action=toggle_vote_status&id=<?php echo $option['id']; ?>" class="btn btn-sm btn-secondary">
                                <?php echo $option['is_active'] ? 'Desativar' : 'Ativar'; ?>
                            </a>
                            <a href="index.php?action=delete_vote&id=<?php echo $option['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta opção?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>