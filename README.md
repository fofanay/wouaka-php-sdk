# Wouaka PHP SDK

[![Latest Stable Version](https://poser.pugx.org/wouaka/sdk/v/stable)](https://packagist.org/packages/wouaka/sdk)
[![Total Downloads](https://poser.pugx.org/wouaka/sdk/downloads)](https://packagist.org/packages/wouaka/sdk)
[![License](https://poser.pugx.org/wouaka/sdk/license)](https://packagist.org/packages/wouaka/sdk)

SDK PHP officiel pour l'API Wouaka - Solutions de scoring crédit et vérification KYC pour l'Afrique de l'Ouest.

## Fonctionnalités

- ✅ **WouakaVerify** - Vérification KYC avec détection de liveness et OCR intelligent
- ✅ **WouakaScore** - Évaluation IEA (Indice d'Évaluabilité Africain) pour scoring crédit
- ✅ **TrustLink** - Audit de projets avec analyse satellite et données de terrain
- ✅ **Webhooks** - Notifications temps réel pour événements critiques
- ✅ Support PHP 7.4+ et 8.0+
- ✅ Gestion automatique des erreurs et retry
- ✅ Documentation complète avec exemples

## Installation

### Via Composer (Recommandé)

```bash
composer require wouaka/sdk
```

### Prérequis

- PHP 7.4 ou supérieur
- Extension JSON activée
- Composer

## Configuration Rapide

```php
<?php

require_once 'vendor/autoload.php';

use Wouaka\WouakaClient;

// Initialiser le client avec votre clé API
$client = new WouakaClient([
    'api_key' => 'wka_live_votre_cle_api',
    'environment' => 'production'  // ou 'test'
]);

// Le client expose 3 modules principaux :
// - $client->kyc (Vérification KYC)
// - $client->iea (Scoring PME)
// - $client->trustlink (Audit projets)
```

## Exemples d'Utilisation

### WouakaVerify - Vérification KYC

```php
<?php

// Vérifier un document d'identité avec liveness
$kycResult = $client->kyc->verify([
    'individual_name' => 'Jean Dupont',
    'document_type' => 'passport',
    'document_number' => 'CI987654321',
    'date_of_birth' => '1990-05-15',
    'nationality' => 'CI',
    'document_image_url' => 'https://example.com/document.jpg',
    'selfie_image_url' => 'https://example.com/selfie.jpg',
    'perform_liveness' => true
]);

echo "Statut : " . $kycResult['verification_status'] . "\n";
echo "Score de confiance : " . ($kycResult['confidence_score'] * 100) . "%\n";
echo "Liveness détecté : " . ($kycResult['liveness_check'] ? 'Oui' : 'Non') . "\n";
echo "Document vérifié : " . ($kycResult['document_verified'] ? 'Oui' : 'Non') . "\n";

if ($kycResult['fraud_detected']) {
    echo "⚠️ Fraude détectée !\n";
}
```

### WouakaScore - Évaluation IEA

```php
<?php

// Calculer le score IEA d'une entreprise
$ieaResult = $client->iea->evaluate([
    'company_name' => 'Tech Solutions SARL',
    'company_id' => 'CI123456789',
    'location' => [
        'lat' => 5.3599,
        'lon' => -4.0083
    ],
    'industry' => 'technology',
    'annual_revenue' => 50000000,
    'employees_count' => 15
]);

echo "Score IEA : " . $ieaResult['iea_score'] . "/100\n";
echo "Niveau de risque : " . $ieaResult['risk_level'] . "\n";
echo "Statut : " . $ieaResult['status'] . "\n";

// Breakdown détaillé des scores
echo "\nDétails :\n";
echo "- Score géographique : " . $ieaResult['geo_score'] . "\n";
echo "- Indice de stabilité : " . $ieaResult['stability_index'] . "\n";
echo "- Résilience au risque : " . $ieaResult['resilience_risk'] . "\n";

// Recommandations
echo "\nRecommandations :\n";
foreach ($ieaResult['recommendations'] as $recommendation) {
    echo "- " . $recommendation . "\n";
}
```

### TrustLink - Audit de Projet

```php
<?php

// Auditer un projet avec analyse satellite
$auditResult = $client->trustlink->audit([
    'project_name' => 'Construction École Primaire',
    'project_type' => 'infrastructure',
    'location' => [
        'lat' => 5.3599,
        'lon' => -4.0083
    ],
    'funding_amount' => 100000000,
    'start_date' => '2024-01-01',
    'expected_completion' => '2024-12-31'
]);

echo "Score TrustLink : " . $auditResult['trustlink_score'] . "/100\n";
echo "Risque projet : " . $auditResult['risk_level'] . "\n";
echo "Progression : " . $auditResult['completion_percentage'] . "%\n";

// Analyse satellite
if (isset($auditResult['satellite_analysis'])) {
    echo "\nAnalyse Satellite :\n";
    echo "- Activité détectée : " . ($auditResult['satellite_analysis']['activity_detected'] ? 'Oui' : 'Non') . "\n";
    echo "- Changement : " . $auditResult['satellite_analysis']['change_percentage'] . "%\n";
}
```

### Webhooks - Validation de Signature

```php
<?php

use Wouaka\Webhooks;

// Recevoir et valider un webhook
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WOUAKA_SIGNATURE'] ?? '';

$webhookSecret = 'whsec_votre_secret_webhook';

if (Webhooks::verifySignature($payload, $signature, $webhookSecret)) {
    $event = json_decode($payload, true);
    
    switch ($event['type']) {
        case 'kyc.verified':
            echo "KYC vérifié : " . $event['data']['verification_id'];
            break;
            
        case 'iea.completed':
            echo "IEA complété : " . $event['data']['evaluation_id'];
            break;
            
        case 'quota.warning':
            echo "⚠️ Alerte quota : " . $event['data']['resource_type'];
            break;
            
        default:
            echo "Événement non géré : " . $event['type'];
    }
} else {
    http_response_code(401);
    echo "Signature invalide";
}
```

## Gestion des Erreurs

```php
<?php

use Wouaka\Exceptions\InvalidAPIKeyException;
use Wouaka\Exceptions\QuotaExceededException;
use Wouaka\Exceptions\WouakaAPIException;

try {
    $result = $client->kyc->verify([
        'individual_name' => 'Jean Dupont',
        'document_type' => 'passport',
        // ... autres paramètres
    ]);
} catch (InvalidAPIKeyException $e) {
    echo "❌ Clé API invalide : " . $e->getMessage();
} catch (QuotaExceededException $e) {
    echo "⚠️ Quota dépassé : " . $e->getMessage();
} catch (WouakaAPIException $e) {
    echo "❌ Erreur API : " . $e->getMessage();
    echo "Code : " . $e->getCode();
}
```

## Configuration Avancée

### Variable d'Environnement URL de Base

Pour utiliser un domaine custom ou un environnement de test :

```php
<?php

// Méthode 1 : Variable d'environnement
putenv('WOUAKA_API_URL=https://api-test.wouaka.com/v1');

$client = new WouakaClient([
    'api_key' => 'wka_test_votre_cle_api'
]);
```

### Timeout et Retry

```php
<?php

$client = new WouakaClient([
    'api_key' => 'wka_live_votre_cle_api',
    'timeout' => 60,  // Timeout en secondes (défaut: 30s)
    'retry_config' => [
        'max_retries' => 3,
        'backoff_factor' => 2,
        'retry_on' => [408, 500, 502, 503, 504]
    ]
]);
```

## Tests

```bash
# Installer les dépendances de développement
composer install

# Exécuter les tests
composer test

# Analyse statique
composer analyse

# Vérifier le code style
composer cs
```

## Support

- **Documentation complète** : https://docs.wouaka.com
- **Email** : support@wouaka.com
- **Téléphone** : +225 07 01 23 89 74
- **GitHub** : https://github.com/wouaka/wouaka-php-sdk

## Licence

MIT License - Copyright (c) 2025 Wouaka SAS

Voir [LICENSE](LICENSE) pour plus de détails.
