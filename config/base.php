<?php
// Define a URL base do projeto
define('PROJECT_NAME', 'tsukuyomi'); // Mude aqui se mudar o nome da pasta
// define('BASE_URL', '/' . PROJECT_NAME . '/public/');
define('FULL_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL);

// Função para criar URLs completas
function makeUrl($action = '', $params = []) {
    $url = BASE_URL . 'index.php';
    
    if ($action) {
        $url .= '?action=' . $action;
    }
    
    foreach ($params as $key => $value) {
        $url .= '&' . $key . '=' . urlencode($value);
    }
    
    return $url;
}

// Função para redirecionar
function redirectTo($action = '', $params = []) {
    $url = makeUrl($action, $params);
    header('Location: ' . $url);
    exit();
}
?>