<?php
// Configurações do sistema

// Define a URL base do projeto
define('BASE_URL', '/Tsukuyomi/public/');

// Define o caminho base do projeto
define('BASE_PATH', dirname(__DIR__) . '/');

// Função helper para criar URLs
function url($action = '', $params = []) {
    $url = BASE_URL . 'index.php';
    
    if ($action) {
        $url .= '?action=' . $action;
    }
    
    foreach ($params as $key => $value) {
        $url .= '&' . $key . '=' . $value;
    }
    
    return $url;
}

// Função para redirecionar
function redirect($action = '', $params = []) {
    header('Location: ' . url($action, $params));
    exit();
}
?>