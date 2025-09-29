<?php
class Vote {
    private $conn;
    private $votes_table = "collection_votes";
    private $user_votes_table = "user_votes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Pega todas as opções de votação ativas
    public function getVoteOptions() {
        $query = "SELECT * FROM " . $this->votes_table . " WHERE is_active = 1 ORDER BY votes DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Verifica se um usuário já votou
    public function hasUserVoted($user_id) {
        $query = "SELECT id FROM " . $this->user_votes_table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Adiciona um voto
    public function addVote($user_id, $vote_option_id) {
        $this->conn->beginTransaction();
        try {
            // 1. Incrementa a contagem de votos na coleção
            $query_increment = "UPDATE " . $this->votes_table . " SET votes = votes + 1 WHERE id = :vote_option_id";
            $stmt_increment = $this->conn->prepare($query_increment);
            $stmt_increment->bindParam(':vote_option_id', $vote_option_id);
            $stmt_increment->execute();

            // 2. Registra que o usuário votou
            $query_user_vote = "INSERT INTO " . $this->user_votes_table . " SET user_id = :user_id, vote_option_id = :vote_option_id";
            $stmt_user_vote = $this->conn->prepare($query_user_vote);
            $stmt_user_vote->bindParam(':user_id', $user_id);
            $stmt_user_vote->bindParam(':vote_option_id', $vote_option_id);
            $stmt_user_vote->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- MÉTODOS DE ADMINISTRAÇÃO ---

    // Pega TODAS as opções de votação (ativas e inativas)
    public function getAllVoteOptions() {
        $query = "SELECT * FROM " . $this->votes_table . " ORDER BY votes DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Cria uma nova opção de votação
    public function createOption($name, $image_url) {
        $query = "INSERT INTO " . $this->votes_table . " SET name = :name, image_url = :image_url";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':image_url', $image_url);
        return $stmt->execute();
    }

    // Ativa ou desativa uma opção de votação
    public function toggleStatus($id) {
        $query = "UPDATE " . $this->votes_table . " SET is_active = !is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Exclui uma opção de votação
    public function deleteOption($id) {
        $query = "DELETE FROM " . $this->votes_table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Reseta toda a votação (COM A CORREÇÃO)
    public function resetAllVotes() {
        $this->conn->beginTransaction();
        try {
            // Zera os votos na tabela de opções
            $query_reset_votes = "UPDATE " . $this->votes_table . " SET votes = 0";
            $this->conn->exec($query_reset_votes);

            // Limpa a tabela de registros de votos de usuários usando DELETE em vez de TRUNCATE
            $query_delete_users = "DELETE FROM " . $this->user_votes_table;
            $this->conn->exec($query_delete_users);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Agora o rollback funcionará, pois não houve commit implícito
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            // Opcional: logar o erro $e->getMessage()
            return false;
        }
    }

    // Busca uma única opção de voto pelo ID
    public function getOptionById($id) {
        $query = "SELECT * FROM " . $this->votes_table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Atualiza uma opção de voto existente
    public function updateOption($id, $name, $image_url) {
        $query = "UPDATE " . $this->votes_table . " SET name = :name, image_url = :image_url WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':image_url', $image_url);
        return $stmt->execute();
    }
}
?>