<?php
    require_once __DIR__ . '/../Service/S3Client.php';
    require_once __DIR__ . '/../../core/Controller.php';
    require_once __DIR__ . '/../Service/AuthService.php';

    class userController extends Controller
    {
        public function userView(){
            
            $authService = new AuthService;
            if(!$authService->checkAuthentication()){
                http_response_code(403);
                exit('Não autorizado.');
            }
            
            $_SESSION['path'] = $_GET['path'] ?? '';

            $id = $this->getLaundryID();

            $objects = $this->getDisplayableObjects($id);

            $this->view(
                'userView',
                [
                    'backups' => $objects,
                    'currentPath' => $_SESSION['path'] ?? '',
                    'parentPath' => $this->parentPath($_SESSION['path']) ?? ''
                ]
            );
        }

        protected function getLaundryID()
        {
            if (!isset($_SESSION['id'])){
                return null;
            }            

            return sprintf('%03d', $_SESSION['id']);
        } 
    }

?>