<?php
class config
{
    private $env;
    private $env_path;
    
    public function __construct($env_path = null)
    {
        $this->env_path = $env_path ?? __DIR__ . '/../.env';
        $this->env = [];    
    }

    private function loadEnvFile(){
        if(!file_exists($this->env_path)){
            throw new RuntimeException("Arquivo .env não encontrado.");
        }

        $env = parse_ini_file($this->env_path);

        if ($env === false){
            throw new RuntimeException("Não foi possível carregar o arquivo .env.");
        }

        $this->env = $env;

        return $this->env;
    }

    private function checkEnvFile($required_keys, $context){
        $missingKeys = [];

        foreach ($required_keys as $key){
            if(!array_key_exists($key, $this->env) || trim((string) $this->env[$key]) === ''){
                $missingKeys[] = $key;
            }
        }

        if(!empty($missingKeys)){
            throw new RuntimeException("Chaves ausentes na configuração de " . $context . ": " . implode(', ', $missingKeys));
        }
    }

    public function loadS3Config(){
        $required_keys = ['access_key', 'secret_key', 'host', 'region'];
        $this->loadEnvFile();
        $this->checkEnvFile($required_keys, "S3");

        return [
            'access_key' => $this->env['access_key'],
            'secret_key' => $this->env['secret_key'],
            'host' => $this->env['host'],
            'region' => $this->env['region']
        ];
    }

    public function loadDBConfig(){
        $required_keys = ['host_db', 'db', 'user_db', 'pass_db', 'charset'];
        $this->loadEnvFile();
        $this->checkEnvFile($required_keys, "Database");

        return [
            'host_db' => $this->env['host_db'],
            'db' => $this->env['db'],
            'user_db' => $this->env['user_db'],
            'pass_db' => $this->env['pass_db'],
            'charset' => $this->env['charset']
        ];
    }

}
?>