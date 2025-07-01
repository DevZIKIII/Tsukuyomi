<?php
// Iniciar sessão uma única vez no início
session_start();

// Incluir configurações
require_once '../config/config.php';

require_once '../controllers/ProductController.php';
require_once '../controllers/UserController.php';
require_once '../controllers/CartController.php';
require_once '../controllers/OrderController.php';
require_once '../controllers/CouponController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch($action) {
    // Product routes
    case 'home':
    case 'products':
        $controller = new ProductController();
        $controller->index();
        break;
        
    case 'product':
        $controller = new ProductController();
        $controller->show($id);
        break;
        
    case 'create_product':
        $controller = new ProductController();
        $controller->create();
        break;
        
    case 'store_product':
        $controller = new ProductController();
        $controller->store();
        break;
        
    case 'edit_product':
        $controller = new ProductController();
        $controller->edit($id);
        break;
        
    case 'update_product':
        $controller = new ProductController();
        $controller->update($id);
        break;
        
    case 'delete_product':
        $controller = new ProductController();
        $controller->destroy($id);
        break;
        
    case 'search':
        $controller = new ProductController();
        $controller->search();
        break;
        
    // User routes
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
        
    case 'authenticate':
        $controller = new UserController();
        $controller->authenticate();
        break;
        
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
        
    case 'store_user':
        $controller = new UserController();
        $controller->store();
        break;
        
    case 'profile':
        $controller = new UserController();
        $controller->profile();
        break;
        
    case 'update_profile':
        $controller = new UserController();
        $controller->update();
        break;
        
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
        
    case 'users':
        $controller = new UserController();
        $controller->index();
        break;
        
    // Cart routes
    case 'cart':
        $controller = new CartController();
        $controller->index();
        break;
        
    case 'add_to_cart':
        $controller = new CartController();
        $controller->add();
        break;
        
    case 'update_cart':
        $controller = new CartController();
        $controller->update();
        break;
        
    case 'remove_from_cart':
        $controller = new CartController();
        $controller->remove($id);
        break;
        
    case 'clear_cart':
        $controller = new CartController();
        $controller->clear();
        break;
        
    // Order routes
    case 'checkout':
        $controller = new CartController();
        $controller->index();
        break;
        
    case 'create_order':
        $controller = new OrderController();
        $controller->create();
        break;
        
    case 'orders':
        $controller = new OrderController();
        $controller->index();
        break;
        
    case 'order':
        $controller = new OrderController();
        $controller->show($id);
        break;
        
    case 'update_order_status':
        $controller = new OrderController();
        $controller->updateStatus();
        break;
        
    default:
        $controller = new ProductController();
        $controller->index();
        break;
    
    // Coupon routes
    case 'coupons':
        $controller = new CouponController();
        $controller->index();
        break;
    
    case 'create_coupon':
        $controller = new CouponController();
        $controller->create();
        break;
    
    case 'store_coupon':
        $controller = new CouponController();
        $controller->store();
        break;
    
    case 'toggle_coupon':
        $controller = new CouponController();
        $controller->toggleStatus($id);
        break;
    
    case 'delete_coupon':
        $controller = new CouponController();
        $controller->delete($id);
        break;
    
    case 'validate_coupon':
        $controller = new CouponController();
        $controller->validate();
        break;
    
    case 'remove_coupon':
        $controller = new CouponController();
        $controller->remove();
        break;

// User edit route (admin)
    case 'edit_user':
        $controller = new UserController();
        $controller->edit($id);
        break;
    
    case 'update_user':
        $controller = new UserController();
        $controller->updateUser($id);
        break;

    case 'all_orders':
        $controller = new OrderController();
        $controller->allOrders();
        break;    
}
?>