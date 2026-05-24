<?php
session_start();

define('BASE_PATH', dirname(__DIR__));
define('DATA_PATH', BASE_PATH . '/data');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

define('USERS_FILE',    DATA_PATH . '/users.json');
define('PRODUCTS_FILE', DATA_PATH . '/products.json');
define('ORDERS_FILE',   DATA_PATH . '/orders.json');

define('SITE_NAME', 'ShopEasy');

require_once __DIR__ . '/../includes/functions.php';
