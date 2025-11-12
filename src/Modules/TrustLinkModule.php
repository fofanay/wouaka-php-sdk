<?php

namespace Wouaka\Modules;

use Wouaka\WouakaClient;

/**
 * Module d'audit TrustLink (WouakaAtlas)
 */
class TrustLinkModule
{
    private WouakaClient $client;
    
    public function __construct(WouakaClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Créer un audit de projet
     * 
     * @param array $data
     * @return array
     */
    public function createAudit(array $data): array
    {
        $files = [];
        $params = [];
        
        // Préparer les images satellite
        if (isset($data['satellite_images'])) {
            foreach ($data['satellite_images'] as $key => $path) {
                $files["satellite_image_{$key}"] = $path;
            }
            unset($data['satellite_images']);
        }
        
        $params = $data;
        
        return $this->client->upload('/trustlink/audits', $files, $params);
    }
    
    /**
     * Récupérer un audit existant
     * 
     * @param string $auditId
     * @return array
     */
    public function getAudit(string $auditId): array
    {
        return $this->client->request('GET', "/trustlink/audits/{$auditId}");
    }
    
    /**
     * Lister les audits
     * 
     * @param array $params
     * @return array
     */
    public function listAudits(array $params = []): array
    {
        return $this->client->request('GET', '/trustlink/audits', $params);
    }
    
    /**
     * Mettre à jour un audit
     * 
     * @param string $auditId
     * @param array $data
     * @return array
     */
    public function updateAudit(string $auditId, array $data): array
    {
        return $this->client->request('PATCH', "/trustlink/audits/{$auditId}", $data);
    }
}
