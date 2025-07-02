<?php
// factories/ModelFactory.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Coupon.php';

/**
 * Factory abstrata para criação de models
 */
abstract class ModelFactory {
    protected $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Método abstrato que deve ser implementado pelas factories concretas
     */
    abstract public function createModel();
}

/**
 * Factory concreta para Product
 */
class ProductFactory extends ModelFactory {
    public function createModel() {
        return new Product($this->db);
    }
    
    /**
     * Cria um produto com dados
     */
    public function createProductWithData($data) {
        $product = $this->createModel();
        
        if (isset($data['name'])) $product->name = $data['name'];
        if (isset($data['description'])) $product->description = $data['description'];
        if (isset($data['price'])) $product->price = $data['price'];
        if (isset($data['category'])) $product->category = $data['category'];
        if (isset($data['size'])) $product->size = $data['size'];
        if (isset($data['stock_quantity'])) $product->stock_quantity = $data['stock_quantity'];
        if (isset($data['image_url'])) $product->image_url = $data['image_url'];
        
        return $product;
    }
}

/**
 * Factory concreta para User
 */
class UserFactory extends ModelFactory {
    public function createModel() {
        return new User($this->db);
    }
    
    /**
     * Cria um usuário cliente
     */
    public function createCustomer($data) {
        $user = $this->createModel();
        $user->user_type = 'customer';
        
        $this->setUserData($user, $data);
        return $user;
    }
    
    /**
     * Cria um usuário admin
     */
    public function createAdmin($data) {
        $user = $this->createModel();
        $user->user_type = 'admin';
        
        $this->setUserData($user, $data);
        return $user;
    }
    
    private function setUserData($user, $data) {
        if (isset($data['name'])) $user->name = $data['name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['password'])) $user->password = $data['password'];
        if (isset($data['phone'])) $user->phone = $data['phone'];
        if (isset($data['address'])) $user->address = $data['address'];
        if (isset($data['city'])) $user->city = $data['city'];
        if (isset($data['state'])) $user->state = $data['state'];
        if (isset($data['zip_code'])) $user->zip_code = $data['zip_code'];
    }
}

/**
 * Factory concreta para Cart
 */
class CartFactory extends ModelFactory {
    public function createModel() {
        return new Cart($this->db);
    }
    
    /**
     * Cria um item de carrinho
     */
    public function createCartItem($user_id, $product_id, $quantity = 1) {
        $cart = $this->createModel();
        $cart->user_id = $user_id;
        $cart->product_id = $product_id;
        $cart->quantity = $quantity;
        
        return $cart;
    }
}

/**
 * Factory concreta para Order
 */
class OrderFactory extends ModelFactory {
    public function createModel() {
        return new Order($this->db);
    }
    
    /**
     * Cria um pedido pendente
     */
    public function createPendingOrder($user_id, $total, $payment_method, $address) {
        $order = $this->createModel();
        $order->user_id = $user_id;
        $order->total_amount = $total;
        $order->status = 'pending';
        $order->payment_method = $payment_method;
        $order->shipping_address = $address;
        
        return $order;
    }
}

/**
 * Factory concreta para Coupon
 */
class CouponFactory extends ModelFactory {
    public function createModel() {
        return new Coupon($this->db);
    }
    
    /**
     * Cria um cupom de porcentagem
     */
    public function createPercentageCoupon($code, $description, $percentage, $min_order = 0) {
        $coupon = $this->createModel();
        $coupon->code = $code;
        $coupon->description = $description;
        $coupon->discount_type = 'percentage';
        $coupon->discount_value = $percentage;
        $coupon->min_order_value = $min_order;
        
        return $coupon;
    }
    
    /**
     * Cria um cupom de valor fixo
     */
    public function createFixedCoupon($code, $description, $value, $min_order = 0) {
        $coupon = $this->createModel();
        $coupon->code = $code;
        $coupon->description = $description;
        $coupon->discount_type = 'fixed';
        $coupon->discount_value = $value;
        $coupon->min_order_value = $min_order;
        
        return $coupon;
    }
}

/**
 * Factory Manager - Gerencia todas as factories
 */
class FactoryManager {
    private static $instance = null;
    private $factories = [];
    
    private function __construct() {
        // Registrar todas as factories
        $this->registerFactory('product', new ProductFactory());
        $this->registerFactory('user', new UserFactory());
        $this->registerFactory('cart', new CartFactory());
        $this->registerFactory('order', new OrderFactory());
        $this->registerFactory('coupon', new CouponFactory());
    }
    
    /**
     * Singleton pattern para garantir uma única instância
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Registra uma nova factory
     */
    public function registerFactory($name, ModelFactory $factory) {
        $this->factories[$name] = $factory;
    }
    
    /**
     * Obtém uma factory específica
     */
    public function getFactory($name) {
        if (!isset($this->factories[$name])) {
            throw new Exception("Factory '$name' não encontrada");
        }
        return $this->factories[$name];
    }
    
    /**
     * Métodos de conveniência
     */
    public function createProduct() {
        return $this->getFactory('product')->createModel();
    }
    
    public function createUser() {
        return $this->getFactory('user')->createModel();
    }
    
    public function createCart() {
        return $this->getFactory('cart')->createModel();
    }
    
    public function createOrder() {
        return $this->getFactory('order')->createModel();
    }
    
    public function createCoupon() {
        return $this->getFactory('coupon')->createModel();
    }
}
?>