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

    private function sendRequest(array $request, string $method){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers']);
        $response = curl_exec($ch);

        if ($response === false){
            throw new RuntimeException('Erro ao executar requesição s3: ' . curl_error($ch));
        }

        curl_close($ch);
        
        return $response;
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
        $response = $this->sendRequest($request, 'GET');
        return $this->parseResponse($response);
    }

    public function downloadObject($object){
        $request = $this->buildRequest('GET', "/$object", '', '');
        $response = $this->sendRequest($request, 'GET');
        return $response;
    } 
}
?>