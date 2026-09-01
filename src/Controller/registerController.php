<?php
    require_once __DIR__ . "/../../core/Controller.php";
    require_once __DIR__ . "/../Database/Db.php";
    require_once __DIR__ . "/../Service/AuthService.php";

    class registerController extends Controller 
    {
        public function registerPage(){
            $authService = new AuthService;
            if(!$authService->checkAuthentication()){
                http_response_code(403);
                exit("Não autorizado");
            }

            $Db = new Db;
            $laundrys = $Db->findAllLaundrys();

            $this->view(
                'RegisterView',
                [
                    'laundrys' => $laundrys
                ]
            );
        }   

        public function register(){
            $DB = new Db;

            $descriptive = trim($_POST['descriptive'] ?? '');
            $id = $_POST['id'] ?? '';
            $password = $_POST['password'] ?? '';

        if ($descriptive === '' || $id === '' || $password === '') {
            http_response_code(400);
            exit('Todos os campos são obrigatórios.');
        }

        if (!ctype_digit((string)$id) || (int)$id < 1 || (int)$id > 99) {
            http_response_code(400);
            exit('ID inválido. Deve ser um número entre 1 e 99.');
        }

        $DB->createLaundry($descriptive, (int)$id, $password);
        header('Location: /admin/register');
        exit;
        }

    }

?>
