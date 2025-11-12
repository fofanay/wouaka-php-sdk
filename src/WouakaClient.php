<?php

namespace Wouaka;

use Wouaka\Modules\KYCModule;
use Wouaka\Modules\IEAModule;
use Wouaka\Modules\TrustLinkModule;
use Wouaka\Exceptions\InvalidAPIKeyException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client principal du SDK Wouaka
 */
class WouakaClient
{
    const VERSION = '1.0.0';
    
    private string $apiKey;
    private string $environment;
    private string $baseUrl;
    private Client $httpClient;
    
    public KYCModule $kyc;
    public IEAModule $iea;
    public TrustLinkModule $trustlink;
    
    /**
     * @param array $config Configuration du client
     * @throws InvalidAPIKeyException
     */
    public function __construct(array $config)
    {
        if (empty($config['api_key'])) {
            throw new InvalidAPIKeyException('La clé API est requise');
        }
        
        $this->apiKey = $config['api_key'];
        $this->environment = $config['environment'] ?? 'production';
        
        // Support de la variable d'environnement WOUAKA_API_URL pour configuration flexible
        $this->baseUrl = getenv('WOUAKA_API_URL') ?: 'https://zepjttggtilxupbzjruj.supabase.co/functions/v1';
        
        // Configuration du client HTTP avec retry
        $retryConfig = $config['retry_config'] ?? [
            'max_retries' => 3,
            'backoff_factor' => 2,
            'retry_on' => [408, 500, 502, 503, 504]
        ];
        
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $config['timeout'] ?? 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Wouaka-PHP-SDK/' . self::VERSION
            ]
        ]);
        
        // Initialiser les modules
        $this->kyc = new KYCModule($this);
        $this->iea = new IEAModule($this);
        $this->trustlink = new TrustLinkModule($this);
    }
    
    /**
     * Effectuer une requête HTTP
     * 
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $options = [];
        
        if ($method === 'GET' && !empty($data)) {
            $options['query'] = $data;
        } elseif (!empty($data)) {
            $options['json'] = $data;
        }
        
        $response = $this->httpClient->request($method, $endpoint, $options);
        
        return json_decode($response->getBody()->getContents(), true);
    }
    
    /**
     * Upload un fichier
     * 
     * @param string $endpoint
     * @param array $files
     * @param array $data
     * @return array
     * @throws GuzzleException
     */
    public function upload(string $endpoint, array $files, array $data = []): array
    {
        $multipart = [];
        
        // Ajouter les fichiers
        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
                'filename' => basename($path)
            ];
        }
        
        // Ajouter les données
        foreach ($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => is_array($value) ? json_encode($value) : $value
            ];
        }
        
        $response = $this->httpClient->request('POST', $endpoint, [
            'multipart' => $multipart
        ]);
        
        return json_decode($response->getBody()->getContents(), true);
    }
    
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
