<?php

require_once '../vendor/autoload.php';

use Wouaka\WouakaClient;
use Wouaka\Exceptions\InvalidDocumentException;
use Wouaka\Exceptions\QuotaExceededException;

// Initialiser le client
$client = new WouakaClient([
    'api_key' => 'wka_live_votre_cle_api',
    'environment' => 'production'
]);

// ========================================
// Exemple 1: Vérification simple d'une CNI
// ========================================

try {
    $result = $client->kyc->verify([
        'document_image' => './documents/cni_ivoirienne.jpg',
        'document_type' => 'national_id',
        'country' => 'CI'
    ]);
    
    echo "✅ Vérification réussie!\n";
    echo "Nom complet: {$result['data']['full_name']}\n";
    echo "Date de naissance: {$result['data']['date_of_birth']}\n";
    echo "Numéro document: {$result['data']['document_number']}\n";
    echo "Score d'authenticité: {$result['authenticity_score']}/100\n";
    echo "Liveness détectée: " . ($result['liveness_detected'] ? 'Oui' : 'Non') . "\n";

} catch (InvalidDocumentException $e) {
    echo "❌ Document rejeté: {$e->getRejectionReason()}\n";
    // Raisons possibles:
    // - "not_a_document" (image de fleurs, paysage, etc.)
    // - "screen_capture" (photo d'écran)
    // - "photocopy" (photocopie)
    // - "blurry" (image floue)

} catch (QuotaExceededException $e) {
    echo "❌ Quota dépassé!\n";
    echo "Limite: {$e->getQuotaLimit()}\n";
    echo "Utilisé: {$e->getQuotaUsed()}\n";
}

// ========================================
// Exemple 2: Vérification avec selfie
// ========================================

$result = $client->kyc->verify([
    'document_image' => './documents/cni.jpg',
    'document_type' => 'national_id',
    'country' => 'SN',
    'selfie_image' => './documents/selfie.jpg',
    'enable_liveness' => true
]);

if ($result['face_match_score'] > 80) {
    echo "✅ Visage correspond au document\n";
} else {
    echo "⚠️ Faible correspondance faciale\n";
}

// ========================================
// Exemple 3: Récupérer une vérification existante
// ========================================

$verification = $client->kyc->getVerification('kyc_abc123');
echo "Statut: {$verification['status']}\n";
echo "Créée le: {$verification['created_at']}\n";

// ========================================
// Exemple 4: Lister toutes les vérifications
// ========================================

$verifications = $client->kyc->listVerifications([
    'limit' => 10,
    'status' => 'verified'
]);

echo "Total vérifications: {$verifications['total']}\n";
foreach ($verifications['data'] as $v) {
    echo "- {$v['data']['full_name']} ({$v['country']})\n";
}

// ========================================
// Exemple 5: Vérification en lot
// ========================================

$batchData = [
    [
        'document_image' => './docs/cni1.jpg',
        'country' => 'CI',
        'document_type' => 'national_id'
    ],
    [
        'document_image' => './docs/cni2.jpg',
        'country' => 'SN',
        'document_type' => 'national_id'
    ],
];

$batchResults = $client->kyc->batchVerify($batchData);

foreach ($batchResults['results'] as $idx => $result) {
    if ($result['success']) {
        $num = $idx + 1;
        echo "✅ Document {$num}: {$result['data']['full_name']}\n";
    } else {
        $num = $idx + 1;
        echo "❌ Document {$num}: {$result['error']}\n";
    }
}
