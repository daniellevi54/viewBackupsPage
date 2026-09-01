<?php
    require_once __DIR__ . "/../Database/Db.php";

    class AuthService
    {
        public function getCurrentUser(){
            return $_SESSION['login'] ?? null;
        }

        public function checkAuthentication(){
            return !empty($_SESSION['authenticated']);
        }

        public function logout(){
            session_unset();
            session_destroy();
            header('Location: /login');
            exit();
        }

        public function validateLogin($login, $pass){
            $DB = new Db;
            $row = $DB->findLaundryByName($login);
            if(($row) AND (password_verify($pass, $row['LVD_SENHA']))){
                session_regenerate_id(true);
                $_SESSION['authenticated'] = true;
                $_SESSION['id'] = $row['LVD_CODIGO'];
                $_SESSION['login'] = $row['LVD_DESCRITIVO'];
            }else {
                return false;
            }
            return true;
        }

        public function requireAuthentication(){
            if (!$this->checkAuthentication()){
                header('Location: /views/login.php');
                exit();
            }
        }
    }
?>