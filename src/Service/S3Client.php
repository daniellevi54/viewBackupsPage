<?php
require_once __DIR__ . "/S3Signer.php";
require_once __DIR__ . "/S3Service.php";
require_once __DIR__ . "/../../config/Config.php";

class S3Client
{
    private $signer;
    private $service;

    public function __construct(){
        $this->signer = new S3Signer();
        $this->service = new S3Service($this->signer);
    }

    public function callService(string $action, string $prefix)
    {
        if (empty($action) || empty($prefix)) {
            throw new InvalidArgumentException('Ação e chave são obrigatórias.');
        }

        if ($action === 'list') {
            return $this->service->listObjects($prefix);
        }

        if ($action === 'download') {
            return $this->service->downloadObject($prefix);
        }

        throw new InvalidArgumentException('Ação inválida: ' . $action);
    }
}
?>