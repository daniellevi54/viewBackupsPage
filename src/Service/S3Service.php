<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/S3Signer.php';

class S3Service
{
    private $signer;
    private $host;

    public function __construct(S3Signer $signer, $host = null)
    {
        $config = new Config();
        $s3Config = $config->loadS3Config();

        $this->signer = $signer;
        $this->host = $host ?? $s3Config['host'];
    }

    private function buildRequest(string $method, string $uri, $queryString, string $payload){
        
        $dateFull = gmdate('Ymd\THis\Z');
        $dateShort = gmdate('Ymd');
        
        
        $authorization = $this->signer->buildAuthorizationHeader(
            $method, 
            $uri, 
            $queryString, 
            $dateFull, 
            $dateShort, 
            $payload
        );

        $url = 'https://' . $this->host . $uri;
        if ($queryString !== '') {
            $url .= '?' . $queryString;
        }

        return [
            'url' => $url, 
            'headers' => [
                'Host: ' . $this->host,
                'Authorization: ' . $authorization,
                'x-amz-date: ' . $dateFull,
                'x-amz-content-sha256: ' . hash("sha256", $payload)
            ]
        ];    
    }

    private function sendRequest(array $request, string $method, string $action){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers']);
        
        // Configura o cURL para jogar o resultado direto para a saída padrão (Browser)
        if ($action === 'list'){
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            return $response;
        }else{
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            return $ch;
        }
        
    }

    private function parseResponse($response){
        $xml = simplexml_load_string($response);
        if ($xml === false){
            throw new RuntimeException('Resposta inválida da S3.');
        }
        return json_decode(json_encode($xml), true);
    }

    public function listObjects($prefix){ 
        $queryString = rawurlencode('delimiter') . '=' . rawurlencode('/')
        . '&' . rawurlencode('list-type') . '=' . rawurlencode('2')
        . '&' . rawurlencode('prefix') . '=' . rawurlencode($prefix); 

        $request = $this->buildRequest('GET', '/', $queryString, '');
        $response = $this->sendRequest($request, 'GET', 'list');
        return $this->parseResponse($response);
    }

    public function downloadObject($object){

        if (ob_get_level()) {
            ob_end_clean();
        }

        $request = $this->buildRequest('GET', "/$object", '', '');
        $response = $this->sendRequest($request, 'GET', 'download');

        // Configura os cabeçalhos HTTP para forçar o download no navegador do cliente
        header('Content-Description: File Transfer');
        header('Content-Type: application/x-7z-compressed');
        header('Content-Disposition: attachment; filename="' . basename($object) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // Executa a requisição e envia os dados diretamente para a saída (navegador)
        $success = curl_exec($response);

        if ($success === false) {
            $error = curl_error($response);
            curl_close($response);
            throw new RuntimeException('Erro ao executar requisição s3 para o navegador: ' . $error);
        }

        $httpCode = curl_getinfo($response, CURLINFO_HTTP_CODE);
        curl_close($response);

        if ($httpCode !== 200) {
            // Nota: Se o status não for 200, os dados enviados ao navegador serão o XML de erro da AWS.
            throw new RuntimeException("Erro da AWS S3 (HTTP $httpCode)");
        }

        exit; // Encerra o script para garantir que nenhum HTML extra suje o binário do arquivo
        return $response;
    } 
}