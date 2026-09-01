<?php
require_once __DIR__ . "/../../core/Controller.php";
require_once __DIR__ . "/../Service/AuthService.php";

class loginController extends Controller
{
    public function loginPage(){
        $this->view(
            'loginView'
        );
    }

    public function checkLogin(){
        $authService = new AuthService;
        if ($authService->validateLogin($_POST['login'], $_POST['password'])){
            if ($_SESSION['login'] === 'admin'){
                header('Location: /admin');
                exit;
            }else{
                header('Location: /backups');
                exit;
            }
        }
        http_response_code(401);
        exit("Login ou senha incorretos");
    }

    public function logout(){
        $authService = new AuthService;
        $authService->logout();
    }
}
?>