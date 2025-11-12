<?php

namespace Wouaka\Modules;

use Wouaka\WouakaClient;

/**
 * Module de vérification KYC (WouakaVerify)
 */
class KYCModule
{
    private WouakaClient $client;
    
    public function __construct(WouakaClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Vérifier un document d'identité
     * 
     * @param array $data
     * @return array
     */
    public function verify(array $data): array
    {
        $files = [];
        $params = [];
        
        // Préparer les fichiers
        if (isset($data['document_image'])) {
            $files['document_image'] = $data['document_image'];
            unset($data['document_image']);
        }
        
        if (isset($data['selfie_image'])) {
            $files['selfie_image'] = $data['selfie_image'];
            unset($data['selfie_image']);
        }
        
        $params = $data;
        
        return $this->client->upload('/kyc/verify', $files, $params);
    }
    
    /**
     * Récupérer une vérification existante
     * 
     * @param string $verificationId
     * @return array
     */
    public function getVerification(string $verificationId): array
    {
        return $this->client->request('GET', "/kyc/verifications/{$verificationId}");
    }
    
    /**
     * Lister les vérifications
     * 
     * @param array $params
     * @return array
     */
    public function listVerifications(array $params = []): array
    {
        return $this->client->request('GET', '/kyc/verifications', $params);
    }
    
    /**
     * Vérification en lot
     * 
     * @param array $verifications
     * @return array
     */
    public function batchVerify(array $verifications): array
    {
        return $this->client->request('POST', '/kyc/batch-verify', [
            'verifications' => $verifications
        ]);
    }
}
