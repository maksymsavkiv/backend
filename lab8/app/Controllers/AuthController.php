<?php
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../Models/UserRepository.php';
class AuthController extends BaseController {
public function status(): never{
    $this->json(['authenticated' => !empty($_SESSION['user_id']), 'username' => $_SESSION['username'] ?? null, 'role' => $_SESSION['role'] ?? 'user']);
}
public function register(): never {
    $data=$this->input();$name=trim((string)($data['username']??''));$pass=(string)($data['password']??'');
    if (!preg_match('/^[a-zA-Z0-9_]{3,40}$/D',$name) || strlen($pass)<8 || strlen($pass)>200)
        $this->json(['error'=>'Логін: 3–40 латинських символів, цифр або _. Пароль: 8–200 символів'],422);
    $repo=new UserRepository();
    if ($repo->find($name)) $this->json(['error'=>'Такий логін вже існує'],409);
        try { $id=$repo->create($name,password_hash($pass,PASSWORD_DEFAULT)); }
        catch(PDOException $e){$this->json(['error'=>'Не вдалося зареєструвати користувача'],409);}
  session_regenerate_id(true);$_SESSION['user_id']=$id;$_SESSION['username']=$name;$_SESSION['role'] = 'user';
  $this->json(['authenticated'=>true,'username'=>$name],201);
 }
public function login(): never {
    $data=$this->input();$name=(string)($data['username']??'');$pass=(string)($data['password']??'');
    $user=(new UserRepository())->find($name);
    if (!$user || !password_verify($pass,$user['password_hash'])) $this->json(['error'=>'Неправильний логін або пароль'],401);
    session_regenerate_id(true);$_SESSION['user_id']=(int)$user['id'];$_SESSION['username']=$user['username'];$_SESSION['role'] = $user['role'];
    $this->json(['authenticated'=>true,'username'=>$user['username']]);
}
public function logout(): never {
    $_SESSION=[];session_regenerate_id(true);
    $this->json(['authenticated'=>false]);
    }
}
