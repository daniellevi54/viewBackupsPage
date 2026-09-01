    <?php

    require_once __DIR__ . "/../Database/Db.php";
    require_once __DIR__ . "/../Service/S3Client.php";
    require_once __DIR__ . "/../../core/Controller.php";
    require_once __DIR__ . "/../Service/AuthService.php";

    Class adminController extends Controller
    {
        public function adminPage(){
            
            $authService = new AuthService;
            if(!$authService->checkAuthentication()){
                http_response_code(403);
                exit("Não autorizado.");
            }

            $_SESSION['path'] = $_GET['path'] ?? '';

            if (isset($_POST['laundry']) && $_POST['laundry'] !== '') {
                $_SESSION['laundry'] = $_POST['laundry'] ?? '';
                $_SESSION['path'] = '';
            }

            $DB = new Db;
            $laundrys = $DB->findAllLaundrys();
    
            $id = $this->getLaundryID();

            $objects = $this->getDisplayableObjects($id);

            $this->view(
                "adminView",
                [
                    'laundrys' => $laundrys,
                    'backups'  => $objects ?? '',
                    'currentPath' => $_SESSION['path'] ?? '',
                    'parentPath' => $this->parentPath($_SESSION['path'] ?? '')
                ]
            );
        }
        
        protected function getLaundryID(){
            if (!isset($_SESSION['laundry'])){
                return null;
            }

            return sprintf('%03d', $_SESSION['laundry']);
        }
    }
    ?>