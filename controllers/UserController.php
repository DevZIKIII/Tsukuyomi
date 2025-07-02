<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../config/base.php';

class UserController {
    private $db;
    private $user;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }
        // Edit user form (admin)
    public function edit($id) {
        if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            redirectTo();
        }
        
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$user) {
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
            $query = "UPDATE users SET 
                     name = :name,
                     email = :email,
                     phone = :phone,
                     address = :address,
                     city = :city,
                     state = :state,
                     zip_code = :zip_code,
                     user_type = :user_type";
            
            // Se uma nova senha foi fornecida
            if(!empty($_POST['new_password'])) {
                $query .= ", password = :password";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':address', $_POST['address']);
            $stmt->bindParam(':city', $_POST['city']);
            $stmt->bindParam(':state', $_POST['state']);
            $stmt->bindParam(':zip_code', $_POST['zip_code']);
            $stmt->bindParam(':user_type', $_POST['user_type']);
            $stmt->bindParam(':id', $id);
            
            if(!empty($_POST['new_password'])) {
                $password_hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                $stmt->bindParam(':password', $password_hash);
            }
            
            if($stmt->execute()) {
                $_SESSION['message'] = "Usuário atualizado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao atualizar usuário.";
            }
            
            redirectTo('users');
        }
    }
    
    // Show login form
    public function login() {
        include '../views/auth/login.php';
    }
    
    // Show register form
    public function register() {
        include '../views/auth/register.php';
    }
    
    // Authenticate user
    public function authenticate() {
        if($_POST) {
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            
            if($this->user->login()) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_type'] = $this->user->user_type;
                
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
            // Validate passwords match
            if($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error'] = "As senhas não coincidem.";
                redirectTo('register');
            }
            
            // Set user properties
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            $this->user->phone = $_POST['phone'];
            $this->user->address = $_POST['address'];
            $this->user->city = $_POST['city'];
            $this->user->state = $_POST['state'];
            $this->user->zip_code = $_POST['zip_code'];
            $this->user->user_type = 'customer';
            
            // Check if email already exists
            if($this->user->emailExists()) {
                $_SESSION['error'] = "Este email já está cadastrado.";
                redirectTo('register');
            }
            
            if($this->user->create()) {
                $_SESSION['message'] = "Conta criada com sucesso! Faça login.";
                redirectTo('login');
            } else {
                $_SESSION['error'] = "Erro ao criar conta.";
                redirectTo('register');
            }
        }
    }
    
    // Show profile
    public function profile() {
        if(!isset($_SESSION['user_id'])) {
            redirectTo('login');
        }
        
        $this->user->id = $_SESSION['user_id'];
        $this->user->readOne();
        
        include '../views/users/profile.php';
    }
    
    // Update user
    public function update() {
        if(!isset($_SESSION['user_id'])) {
            redirectTo('login');
        }
        
        if($_POST) {
            $this->user->id = $_SESSION['user_id'];
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->phone = $_POST['phone'];
            $this->user->address = $_POST['address'];
            $this->user->city = $_POST['city'];
            $this->user->state = $_POST['state'];
            $this->user->zip_code = $_POST['zip_code'];
            
            if($this->user->update()) {
                $_SESSION['user_name'] = $this->user->name;
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
        
        $stmt = $this->user->read();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../views/users/index.php';
    }
}
?>