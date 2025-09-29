<?php include '../views/layout/header.php'; ?>

<div class="vote-container">
    <div class="vote-header">
        <h1>Vote na Próxima Coleção</h1>
        <p>Ajude-nos a decidir qual será o próximo universo aterrissar na Tsukuyomi!</p>
    </div>

    <?php if ($has_voted): ?>
        <div class="alert alert-success">
            <strong>Obrigado por participar!</strong> Seu voto já foi computado.
        </div>
    <?php endif; ?>

    <div class="vote-grid">
        <?php 
        $total_votes = 0;
        foreach ($vote_options as $option) {
            $total_votes += $option['votes'];
        }

        foreach ($vote_options as $option): 
            $percentage = ($total_votes > 0) ? ($option['votes'] / $total_votes) * 100 : 0;
        ?>
            <div class="vote-card <?php echo $has_voted ? 'voted' : ''; ?>">
                <div class="vote-image-container">
                    <img src="images/products/<?php echo htmlspecialchars($option['image_url']); ?>" alt="<?php echo htmlspecialchars($option['name']); ?>" class="vote-image">
                </div>
                <div class="vote-info">
                    <h3><?php echo htmlspecialchars($option['name']); ?></h3>
                    <?php if ($has_voted): ?>
                        <div class="vote-results">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                            </div>
                            <span class="vote-count"><?php echo number_format($percentage, 1); ?>% (<?php echo $option['votes']; ?> votos)</span>
                        </div>
                    <?php else: ?>
                        <form action="index.php?action=add_vote" method="POST">
                            <input type="hidden" name="vote_option_id" value="<?php echo $option['id']; ?>">
                            <button type="submit" class="btn btn-primary">Votar neste!</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.vote-container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
.vote-header { text-align: center; margin-bottom: 3rem; }
.vote-header p { font-size: 1.2rem; color: var(--text-secondary); }
.vote-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
.vote-card { background: var(--surface-color); border-radius: var(--border-radius-xl); overflow: hidden; text-align: center; border: 1px solid var(--border-color); transition: all 0.3s ease; display: flex; flex-direction: column; }
.vote-card:not(.voted):hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--primary-color); }

/* --- NOVO ESTILO PARA DEIXAR A IMAGEM QUADRADA --- */
.vote-image-container {
    width: 100%;
    aspect-ratio: 1 / 1; /* Força o container a ser um quadrado perfeito */
    background-color: #1a1a1a; /* Cor de fundo caso a imagem não carregue */
}
.vote-image {
    width: 100%;
    height: 100%; /* Faz a imagem preencher o container quadrado */
    object-fit: cover; /* Garante que a imagem cubra o espaço sem distorcer */
}
/* --- FIM DO NOVO ESTILO --- */

.vote-info { padding: 2rem; }
.vote-info h3 { font-size: 1.5rem; margin-bottom: 1.5rem; }
.progress-bar { width: 100%; background-color: var(--border-color); border-radius: 5px; height: 10px; margin-bottom: 0.5rem; }
.progress-fill { height: 100%; background-color: var(--primary-color); border-radius: 5px; }
.vote-count { font-weight: bold; color: var(--primary-color); }
</style>


<?php include '../views/layout/footer.php'; ?>