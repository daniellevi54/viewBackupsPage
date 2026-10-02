<?php
require_once __DIR__ . "/../../config/Config.php";

class Db
{
    private $connection;

    public function __construct()
    {
        $config = new Config();
        $DBConfig = $config->loadDBConfig();

        $this->connection = ibase_connect(
            $DBConfig['host_db'] . ":" . $DBConfig['db'],
            $DBConfig['user_db'],
            $DBConfig['pass_db'],
            $DBConfig['charset']
        );

        if (!$this->connection){
            throw new RuntimeException('Falha na Conexão com o Banco ' . ibase_errmsg());
        }
    }
    
    public function findAllLaundrys(){
        $sql='SELECT LVD_CODIGO, LVD_DESCRITIVO FROM lavanderias ';
        $sql .='WHERE LVD_CODIGO > 0 ORDER BY LVD_DESCRITIVO ASC';

        $result = ibase_query($this->connection, $sql);

        if(!$result){
            throw new RuntimeException('Erro na consulta: ' . ibase_errmsg());
        }
    
        $lavanderias = [];

        while($row = ibase_fetch_assoc($result)){
            $lavanderias[$row['LVD_DESCRITIVO']] = $row['LVD_CODIGO'];
        }

        ibase_free_result($result);
        return $lavanderias;
    }

    public function createLaundry($lnd_name, $lnd_id, $lnd_pass){
        $hashPassword = password_hash($lnd_pass, PASSWORD_DEFAULT);
    
        $sql = "INSERT INTO lavanderias (LVD_CODIGO, LVD_DESCRITIVO, LVD_SENHA) VALUES (?, ?, ?)";
        
        $result = ibase_query($this->connection, $sql, $lnd_id, $lnd_name, $lnd_pass);
        
        if (!$result){
            throw new RuntimeException("Erro ao criar lavanderia: " . ibase_errmsg());
        }

        return true;
    }

    public function findLaundryByName($name){
        $sql = "SELECT * FROM lavanderias ";
        $sql .= "WHERE LVD_DESCRITIVO = ?";

        $result = ibase_query($this->connection, $sql, $name);

        if (!$result){
            throw new RuntimeException("Erro na Consulta: " . ibase_errmsg());
        }

        $row = ibase_fetch_assoc($result);
        ibase_free_result($result);

        return $row ?: null;
    }

    public function __destruct()
    {
        if ($this->connection){
            ibase_close($this->connection);
        }
    }
}
?>