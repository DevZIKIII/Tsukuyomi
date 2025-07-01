<?php include '../views/layout/header.php'; ?>

<h2>Gerenciar Usuários</h2>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Tipo</th>
                <th>Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td>
                        <span class="badge <?php echo $user['user_type'] == 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                            <?php echo $user['user_type']; ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="/tsukuyomi/public/index.php?action=edit_user&id=<?php echo $user['id']; ?>" 
                           class="btn btn-sm btn-secondary">Editar</a>
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="/tsukuyomi/public/index.php?action=delete_user&id=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.table-container {
    overflow-x: auto;
    margin: 2rem 0;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    background-color: var(--surface-color);
    font-weight: 600;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.badge-primary {
    background-color: var(--primary-color);
    color: white;
}

.badge-secondary {
    background-color: var(--border-color);
    color: var(--text-secondary);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>

<?php include '../views/layout/footer.php'; ?>