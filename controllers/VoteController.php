<?php
require_once '../factories/ModelFactory.php';

class VoteController {
    private $voteFactory;
    // Não precisamos mais do productFactory aqui

    public function __construct() {
        $factoryManager = FactoryManager::getInstance();
        $this->voteFactory = $factoryManager->getFactory('vote');
    }

    // ... (os métodos index() e add() para clientes continuam os mesmos) ...
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Você precisa estar logado para votar.";
            header('Location: index.php?action=login');
            exit();
        }
        $vote = $this->voteFactory->createModel();
        $stmt = $vote->getVoteOptions();
        $vote_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $has_voted = $vote->hasUserVoted($_SESSION['user_id']);
        include '../views/votes/index.php';
    }

    public function add() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Ação não permitida.";
            header('Location: index.php?action=vote');
            exit();
        }
        if (isset($_POST['vote_option_id'])) {
            $vote = $this->voteFactory->createModel();
            if ($vote->hasUserVoted($_SESSION['user_id'])) {
                $_SESSION['error'] = "Você já votou.";
                header('Location: index.php?action=vote');
                exit();
            }
            if ($vote->addVote($_SESSION['user_id'], $_POST['vote_option_id'])) {
                $_SESSION['message'] = "Obrigado! Seu voto foi registrado com sucesso.";
            } else {
                $_SESSION['error'] = "Ocorreu um erro ao registrar seu voto.";
            }
        }
        header('Location: index.php?action=vote');
        exit();
    }

    // --- AÇÕES DE ADMINISTRAÇÃO ---

    public function adminIndex() {
        $this->checkAdmin();
        $vote = $this->voteFactory->createModel();
        $stmt = $vote->getAllVoteOptions();
        $vote_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include '../views/votes/admin_index.php';
    }

    // MODIFICADO: Lê os arquivos da pasta de imagens
    public function create() {
        $this->checkAdmin();
        
        // Caminho para a pasta de imagens dos produtos
        $imagesPath = '../public/images/products/';
        $allFiles = scandir($imagesPath);
        
        // Filtra para pegar apenas arquivos de imagem (e não '.' ou '..')
        $product_images = array_filter($allFiles, function($file) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($fileExtension, $imageExtensions);
        });

        include '../views/votes/create.php';
    }

    // MODIFICADO: Salva a opção com base na imagem selecionada
    public function store() {
        $this->checkAdmin();
        
        if ($_POST && !empty($_POST['name']) && !empty($_POST['image_url'])) {
            $vote = $this->voteFactory->createModel();
            $name = $_POST['name'];
            $imageUrl = $_POST['image_url'];

            if ($vote->createOption($name, $imageUrl)) {
                $_SESSION['message'] = "Nova opção de voto adicionada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao adicionar nova opção.";
            }
        } else {
            $_SESSION['error'] = "Dados inválidos. O nome e a seleção de uma imagem são obrigatórios.";
        }
        
        header('Location: index.php?action=admin_votes');
        exit();
    }

    // ... (os outros métodos de admin toggleStatus, delete, reset continuam os mesmos) ...

    public function toggleStatus($id) {
        $this->checkAdmin();
        $vote = $this->voteFactory->createModel();
        if ($vote->toggleStatus($id)) {
            $_SESSION['message'] = "Status da opção alterado com sucesso.";
        } else {
            $_SESSION['error'] = "Erro ao alterar o status.";
        }
        header('Location: index.php?action=admin_votes');
        exit();
    }

    public function delete($id) {
        $this->checkAdmin();
        $vote = $this->voteFactory->createModel();
        if ($vote->deleteOption($id)) {
            $_SESSION['message'] = "Opção de voto excluída com sucesso.";
        } else {
            $_SESSION['error'] = "Erro ao excluir a opção.";
        }
        header('Location: index.php?action=admin_votes');
        exit();
    }
    
    public function reset() {
        $this->checkAdmin();
        $vote = $this->voteFactory->createModel();
        if ($vote->resetAllVotes()) {
            $_SESSION['message'] = "Toda a votação foi resetada com sucesso.";
        } else {
            $_SESSION['error'] = "Erro ao resetar a votação.";
        }
        header('Location: index.php?action=admin_votes');
        exit();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
            header('Location: index.php');
            exit();
        }
    }

    // Exibe a página de edição com os dados preenchidos
    public function edit($id) {
        $this->checkAdmin();
        
        $vote = $this->voteFactory->createModel();
        $vote_option = $vote->getOptionById($id);

        if (!$vote_option) {
            $_SESSION['error'] = "Opção de voto não encontrada.";
            header('Location: index.php?action=admin_votes');
            exit();
        }

        // Pega a lista de imagens para o dropdown
        $imagesPath = '../public/images/products/';
        $allFiles = scandir($imagesPath);
        $product_images = array_filter($allFiles, function($file) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($fileExtension, $imageExtensions);
        });

        include '../views/votes/edit.php';
    }

    // Processa a atualização dos dados
    public function update($id) {
        $this->checkAdmin();

        if ($_POST && !empty($_POST['name']) && !empty($_POST['image_url'])) {
            $vote = $this->voteFactory->createModel();
            $name = $_POST['name'];
            $imageUrl = $_POST['image_url'];

            if ($vote->updateOption($id, $name, $imageUrl)) {
                $_SESSION['message'] = "Opção de voto atualizada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao atualizar a opção de voto.";
            }
        } else {
            $_SESSION['error'] = "Dados inválidos.";
        }
        
        header('Location: index.php?action=admin_votes');
        exit();
    }
}
?>