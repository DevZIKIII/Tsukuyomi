<?php
require_once '../factories/ModelFactory.php';
require_once '../config/base.php';

class UserController {
    private $userFactory;
    
    public function __construct() {
        // Usar o FactoryManager para obter a factory de usuários
        $factoryManager = FactoryManager::getInstance();
        $this->userFactory = $factoryManager->getFactory('user');
    }
    
    // Show login form
    public function login() {
        include '../views/users/login.php';
    }
    
    // Show register form
    public function register() {
        include '../views/users/register.php';
    }
    
    // Authenticate user
    public function authenticate() {
        if($_POST) {
            $user = $this->userFactory->createModel();
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            
            if($user->login()) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_type'] = $user->user_type;
                
                redirectTo('products');
            } else {
                $_SESSION['error'] = "Email ou senha incorretos.";
                redirectTo('login');
            }
        }
    }
    
    // Store new user
    public function store() {
        if($_POST) {
            if(!isset($_POST['password']) || !isset($_POST['confirm_password']) || $_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error'] = "As senhas não coincidem.";
                redirectTo('register');
                return;
            }
            
            // --- LÓGICA DE ENDEREÇO CORRIGIDA ---
            // Junta os campos de endereço em uma única string para salvar no banco
            $address_parts = [];
            if (!empty($_POST['address'])) $address_parts[] = $_POST['address']; // Rua
            if (!empty($_POST['number'])) $address_parts[] = $_POST['number'];   // Número
            if (!empty($_POST['complement'])) $address_parts[] = $_POST['complement']; // Complemento
            $full_address = implode(', ', $address_parts);
            // --- FIM DA CORREÇÃO ---

            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'],
                'phone' => $_POST['phone'] ?? null,
                'address' => $full_address, // Salva o endereço completo
                'city' => $_POST['city'] ?? null,
                'state' => $_POST['state'] ?? null,
                'zip_code' => $_POST['zip_code'] ?? null
            ];
            
            $user = $this->userFactory->createCustomer($userData);
            
            if($user->emailExists()) {
                $_SESSION['error'] = "Este email já está cadastrado.";
                redirectTo('register');
                return;
            }
            
            if($user->create()) {
                $_SESSION['message'] = "Conta criada com sucesso! Faça login.";
                redirectTo('login');
            } else {
                $_SESSION['error'] = "Erro ao criar conta. Tente novamente.";
                redirectTo('register');
            }
        }
    }
    
    // Show profile
    public function profile() {
    if(!isset($_SESSION['user_id'])) {
        redirectTo('login');
    }
    
    $user = $this->userFactory->createModel();
    $user->id = $_SESSION['user_id'];
    
    // Buscar dados do usuário
    if($user->readOne()) {
        // Dados encontrados, prosseguir para a view
        include '../views/users/profile.php';
    } else {
        // Usuário não encontrado, redirecionar
        $_SESSION['error'] = "Erro ao carregar perfil do usuário.";
        redirectTo('login');
    }
}
    
    // Update user
    public function update() {
        if(!isset($_SESSION['user_id'])) {
            redirectTo('login');
        }
        
        if($_POST) {
            $user = $this->userFactory->createModel();
            $user->id = $_SESSION['user_id'];
            $user->name = $_POST['name'];
            $user->email = $_POST['email'];
            $user->phone = $_POST['phone'];
            
            // --- LÓGICA DE ENDEREÇO CORRIGIDA ---
            // Junta os campos de endereço em uma única string para salvar no banco
            $address_parts = [];
            if (!empty($_POST['address'])) $address_parts[] = $_POST['address']; // Rua
            if (!empty($_POST['number'])) $address_parts[] = $_POST['number'];   // Número
            if (!empty($_POST['complement'])) $address_parts[] = $_POST['complement']; // Complemento
            $user->address = implode(', ', $address_parts);
            // --- FIM DA CORREÇÃO ---

            $user->city = $_POST['city'];
            $user->state = $_POST['state'];
            $user->zip_code = $_POST['zip_code'];
            
            if($user->update()) {
                $_SESSION['user_name'] = $user->name;
                $_SESSION['message'] = "Perfil atualizado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao atualizar perfil.";
            }
            
            redirectTo('profile');
        }
    }
    
    // Logout
    public function logout() {
        session_destroy();
        redirectTo();
    }
    
    // List all users (admin only)
    public function index() {
        if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        $user = $this->userFactory->createModel();
        $stmt = $user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/users/index.php';
    }
    
    // Show user details (admin)
    public function show($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        $user = $this->userFactory->createModel();
        $user->id = $id;
        $user->readOne();
        
        include '../views/users/show.php';
    }
    
    // Create user form (admin)
    public function create() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        include '../views/users/create.php';
    }
    
    // Store user (admin)
    public function storeAdmin() {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        if($_POST) {
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'zip_code' => $_POST['zip_code']
            ];
            
            // Criar usuário baseado no tipo
            if($_POST['user_type'] === 'admin') {
                $user = $this->userFactory->createAdmin($userData);
            } else {
                $user = $this->userFactory->createCustomer($userData);
            }
            
            if($user->emailExists()) {
                $_SESSION['error'] = "Este email já está cadastrado.";
                redirectTo('create_user');
            }
            
            if($user->create()) {
                $_SESSION['message'] = "Usuário criado com sucesso!";
                redirectTo('users');
            } else {
                $_SESSION['error'] = "Erro ao criar usuário.";
                redirectTo('create_user');
            }
        }
    }
    
    // Edit user form (admin)
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        $user = $this->userFactory->createModel();
        $user->id = $id;
        $user->readOne();
        
        if(!$user->id) {
            $_SESSION['error'] = "Usuário não encontrado.";
            redirectTo('users');
        }
        
        include '../views/users/edit.php';
    }
    
    // Update user (admin)
    public function updateUser($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        if($_POST) {
            $user = $this->userFactory->createModel();
            $user->id = $id;
            $user->name = $_POST['name'];
            $user->email = $_POST['email'];
            $user->phone = $_POST['phone'];
            $user->address = $_POST['address'];
            $user->city = $_POST['city'];
            $user->state = $_POST['state'];
            $user->zip_code = $_POST['zip_code'];
            $user->user_type = $_POST['user_type'];
            
            // Se uma nova senha foi fornecida
            if(!empty($_POST['new_password'])) {
                $user->password = $_POST['new_password'];
            }
            
            if($user->update()) {
                $_SESSION['message'] = "Usuário atualizado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao atualizar usuário.";
            }
            
            redirectTo('users');
        }
    }
    
    // Delete user (admin)
    public function delete($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        $user = $this->userFactory->createModel();
        $user->id = $id;
        
        if($user->delete()) {
            $_SESSION['message'] = "Usuário excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir usuário.";
        }
        
        redirectTo('users');
    }
}
?>