<?php
    $routes = [
        'GET /' => ['loginController', 'loginPage'],
        'GET /login' => ['loginController', 'loginPage'],
        'POST /login' => ['loginController', 'checkLogin'],

        'GET /admin' => ['adminController', 'adminPage'],
        'POST /admin' => ['adminController', 'adminPage'],

        'GET /admin/register' => ['registerController', 'registerPage'],
        'POST /admin/newRegister' => ['registerController', 'register'],

        'GET /admin/download' => ['adminController', 'download'],
        'GET /download' => ['userController', 'download'],

        'GET /backups' => ['userController', 'userView'],

        'GET /logout' => ['loginController', 'logout']
    ];

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($uri !== '/' && substr($uri, -1, 1) === '/'){
        $uri = rtrim($uri, '/');
    }

    $key = "$method $uri"; 
    
    if(!array_key_exists($key, $routes)){
        http_response_code(404);
        require __DIR__ . "/../views/404.php";
        exit;
    }

    [$controllerName, $action] = $routes[$key];
    $controllerFile = __DIR__ . "/../src/Controller/{$controllerName}.php";

    if(!file_exists($controllerFile)){
        http_response_code(500);
        exit("Arquivo não encontrado: {$controllerName}");
    }

    require $controllerFile;
    $controller = new $controllerName;

    if(!method_exists($controller, $action)){
        http_response_code(500);
        exit("Método não encontrado: {$action}");
    }

    $controller->$action();
?>