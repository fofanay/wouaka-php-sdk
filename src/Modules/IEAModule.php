<?php

namespace Wouaka\Modules;

use Wouaka\WouakaClient;

/**
 * Module d'évaluation IEA (WouakaScore)
 */
class IEAModule
{
    private WouakaClient $client;
    
    public function __construct(WouakaClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Évaluer une entreprise
     * 
     * @param array $data
     * @return array
     */
    public function evaluate(array $data): array
    {
        return $this->client->request('POST', '/iea/evaluate', $data);
    }
    
    /**
     * Récupérer une évaluation existante
     * 
     * @param string $evaluationId
     * @return array
     */
    public function getEvaluation(string $evaluationId): array
    {
        return $this->client->request('GET', "/iea/evaluations/{$evaluationId}");
    }
    
    /**
     * Générer un rapport PDF
     * 
     * @param string $evaluationId
     * @param array $options
     * @return string Binary PDF content
     */
    public function generateReport(string $evaluationId, array $options = []): string
    {
        $params = array_merge(['format' => 'pdf'], $options);
        $response = $this->client->request('GET', "/iea/evaluations/{$evaluationId}/report", $params);
        
        return $response['pdf_content'] ?? '';
    }
    
    /**
     * Évaluation en lot
     * 
     * @param array $businesses
     * @return array
     */
    public function batchEvaluate(array $businesses): array
    {
        return $this->client->request('POST', '/iea/batch-evaluate', [
            'businesses' => $businesses
        ]);
    }
    
    /**
     * Obtenir la recommandation par score
     * 
     * @param int $ieaScore
     * @return array
     */
    public function getRecommendation(int $ieaScore): array
    {
        return $this->client->request('GET', '/iea/recommendation', [
            'score' => $ieaScore
        ]);
    }
}
