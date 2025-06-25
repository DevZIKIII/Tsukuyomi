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
            // if($this->user->emailExists()) {
            //     $_SESSION['error'] = "Este email já está cadastrado.";
            //     redirectTo('register');
            // }
            
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