<?php

namespace Wouaka;

/**
 * Utilitaires pour la gestion des webhooks Wouaka
 */
class Webhooks
{
    /**
     * Vérifier la signature d'un webhook Wouaka
     * 
     * @param string $payload Corps de la requête webhook
     * @param string $signature Signature reçue dans le header X-Wouaka-Signature
     * @param string $webhookSecret Votre secret webhook
     * @return bool
     */
    public static function verifySignature(
        string $payload,
        string $signature,
        string $webhookSecret
    ): bool {
        // Calculer le HMAC SHA-256
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        // Comparer de manière sécurisée (timing-safe)
        return hash_equals($expectedSignature, $signature);
    }
    
    /**
     * Parser le payload d'un événement webhook
     * 
     * @param string $payload Corps de la requête
     * @return array
     */
    public static function parseEvent(string $payload): array
    {
        return json_decode($payload, true);
    }
    
    /**
     * Types d'événements webhook disponibles
     */
    const WEBHOOK_EVENTS = [
        // KYC
        'kyc.verified' => 'Vérification KYC complétée avec succès',
        'kyc.failed' => 'Vérification KYC échouée',
        
        // IEA
        'evaluation.completed' => 'Évaluation IEA complétée',
        'evaluation.failed' => 'Évaluation IEA échouée',
        
        // TrustLink
        'audit.completed' => 'Audit TrustLink complété',
        'audit.updated' => 'Audit TrustLink mis à jour',
        
        // Alertes
        'alert.generated' => 'Alerte système générée',
        'quota.warning' => 'Avertissement quota (90% consommé)',
        'quota.exceeded' => 'Quota dépassé',
    ];
}
