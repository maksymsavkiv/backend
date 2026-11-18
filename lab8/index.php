<?php
declare(strict_types=1);
ini_set('session.use_strict_mode','1');
ini_set('session.cookie_httponly','1');
ini_set('session.cookie_samesite','Lax');
session_start();
require_once __DIR__.'/app/Controllers/AuthController.php';
require_once __DIR__.'/app/Controllers/RecordsController.php';
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$method=$_SERVER['REQUEST_METHOD'];
try {
    if($path==='/' || $path==='/index.html'){
        header('Content-Type: text/html; charset=utf-8');
        require __DIR__.'/app/Views/home.php';exit;
    }
    if($path==='/api/auth/status' && $method==='GET')(new AuthController())->status();
    if($path==='/api/auth/register' && $method==='POST')(new AuthController())->register();
    if($path==='/api/auth/login' && $method==='POST')(new AuthController())->login();
    if($path==='/api/auth/logout' && $method==='POST')(new AuthController())->logout();
    $types=['/api/categories-json.php'=>'categories','/api/property-json.php'=>'properties','/api/example-json.php'=>'examples'];
    if(isset($types[$path])){
        $controller=new RecordsController();
        if($method==='GET')$controller->list($types[$path]);
        if($method==='POST')$controller->change($types[$path]);
    }
    if($path==='/api/upload.php' && $method==='POST')(new RecordsController())->upload();
    http_response_code(404);header('Content-Type: application/json; charset=utf-8');echo json_encode(['error'=>'Маршрут не знайдено']);
} 
catch(Throwable $e){
    error_log($e->getMessage());
    http_response_code(500);header('Content-Type: application/json; charset=utf-8');echo json_encode(['error'=>'Помилка сервера']);
}
