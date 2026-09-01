<?php
require_once __DIR__ . '/../../config/Config.php';

class S3Signer
{
    private $accessKey;
    private $secretKey;
    private $host;
    private $region;

    public function __construct($accessKey = null, $secretKey = null, $host = null, $region = null)
    {
        $config = new Config;
        $s3Config = $config->loadS3Config();

        $this->accessKey = $accessKey ?? $s3Config['access_key'];
        $this->secretKey = $secretKey ?? $s3Config['secret_key'];
        $this->host = $host ?? $s3Config['host'];
        $this->region = $region ?? $s3Config['region'];
    }

    private function buildCanonicalRequest($method, $uri, $queryString, $dateFull, $payload = '')
    {
        $headers = "host:" . $this->host . "\n" .
            "x-amz-content-sha256:" . hash('sha256', $payload) . "\n" .
            "x-amz-date:" . $dateFull;

        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';
        $hashedPayload = hash('sha256', $payload);

        $canonicalRequest = $method . "\n" .
            $uri . "\n" .
            $queryString . "\n" .
            $headers . "\n\n" .
            $signedHeaders . "\n" .
            $hashedPayload;

        return hash('sha256', $canonicalRequest);
    }

    private function buildStringToSign($method, $uri, $path, $dateFull, $dateShort, $payload = '')
    {
        $canonicalRequestHash = $this->buildCanonicalRequest($method, $uri, $path, $dateFull, $payload);

        return "AWS4-HMAC-SHA256\n" .
            $dateFull . "\n" .
            $dateShort . "/" . $this->region . "/s3/aws4_request\n" .
            $canonicalRequestHash;
    }

    private function calculateSignature($method, $uri, $path, $dateFull, $dateShort, $payload = '')
    {
        $dateKey = hash_hmac('sha256', $dateShort, 'AWS4' . $this->secretKey, true);
        $dateRegionKey = hash_hmac('sha256', $this->region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256','s3', $dateRegionKey, true);
        $signingKey = hash_hmac('sha256','aws4_request', $dateRegionServiceKey, true);

        $stringToSign = $this->buildStringToSign($method, $uri, $path, $dateFull, $dateShort, $payload);

        return hash_hmac('sha256', $stringToSign, $signingKey, false);
    }

    public function buildAuthorizationHeader($method, $uri, $path, $dateFull, $dateShort, $payload = '')
    {
        $signature = $this->calculateSignature($method, $uri, $path, $dateFull, $dateShort, $payload);
        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';

        return 'AWS4-HMAC-SHA256 Credential=' . $this->accessKey . '/' . $dateShort . '/' . $this->region . '/s3/aws4_request, SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;
    }
}
