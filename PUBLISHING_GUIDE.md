# Guide de Publication du SDK PHP Wouaka sur Packagist

Ce guide explique comment publier le SDK PHP Wouaka sur Packagist pour permettre l'installation via `composer require wouaka/sdk`.

## Prérequis

1. **Compte Packagist** : Créer un compte sur [packagist.org](https://packagist.org/)
2. **Compte GitHub** : Le SDK doit être hébergé sur GitHub
3. **Repository Public** : Le repository GitHub doit être public
4. **Composer Installé** : Pour les tests locaux

## Étape 1 : Créer un Repository GitHub Public

Si ce n'est pas déjà fait, créer un repository public GitHub pour le SDK PHP :

```bash
# Nom suggéré : wouaka/wouaka-php-sdk
# URL : https://github.com/wouaka/wouaka-php-sdk
```

## Étape 2 : Créer un Compte Packagist

1. Aller sur [packagist.org](https://packagist.org/)
2. Cliquer sur "Sign Up" (ou connectez-vous avec GitHub)
3. Vérifier votre email
4. Aller dans votre profil → Settings → API Token
5. Générer un API Token (pour GitHub Actions)
6. **Copier et sauvegarder ce token** (on en aura besoin pour GitHub Secrets)

## Étape 3 : Soumettre le Package sur Packagist

### Via l'Interface Web (Première fois uniquement)

1. Se connecter sur [packagist.org](https://packagist.org/)
2. Cliquer sur "Submit" dans le menu
3. Entrer l'URL de votre repository GitHub : `https://github.com/wouaka/wouaka-php-sdk`
4. Cliquer sur "Check" puis "Submit"

### Configuration du Webhook GitHub (Auto-update)

Packagist créera automatiquement un webhook sur votre repository GitHub pour mettre à jour le package à chaque push. Vérifiez dans :

```
GitHub → Settings → Webhooks
```

Vous devriez voir un webhook Packagist pointant vers `https://packagist.org/api/github`

## Étape 4 : Configurer GitHub Actions (Automatisation)

### 4.1 Ajouter le Token Packagist aux GitHub Secrets

1. Aller sur votre repository GitHub : `wouaka/wouaka-php-sdk`
2. Settings → Secrets and variables → Actions
3. Cliquer sur "New repository secret"
4. Nom : `PACKAGIST_API_TOKEN`
5. Value : Coller votre token Packagist de l'Étape 2
6. Cliquer sur "Add secret"

### 4.2 Le Workflow GitHub Actions est Prêt

Le fichier `.github/workflows/publish-php-sdk.yml` est déjà créé et va :
- Se déclencher automatiquement à chaque tag `v*` (ex: v1.0.0)
- Exécuter les tests PHPUnit
- Valider le code avec PHPStan et PHPCS
- Notifier Packagist pour mettre à jour le package

## Étape 5 : Publier la Version 1.0.0

### Option A : Via l'Interface GitHub (Recommandé)

1. Aller sur votre repository GitHub
2. Cliquer sur "Releases" (colonne de droite)
3. Cliquer sur "Create a new release"
4. Dans "Choose a tag" :
   - Taper : `v1.0.0`
   - Cliquer sur "Create new tag: v1.0.0 on publish"
5. Release title : `v1.0.0 - Initial Release`
6. Description :
   ```markdown
   # Wouaka PHP SDK v1.0.0
   
   Première version stable du SDK PHP Wouaka
   
   ## Features
   - ✅ Module KYC (Vérification d'identité)
   - ✅ Module IEA (Scoring PME)
   - ✅ Module TrustLink (Audit projets)
   - ✅ Support Webhooks
   - ✅ Gestion erreurs complète
   - ✅ Support PHP 7.4+ et 8.0+
   
   ## Installation
   ```bash
   composer require wouaka/sdk
   ```
   
   ## Documentation
   https://docs.wouaka.com
   ```
7. Cocher "Set as the latest release"
8. Cliquer sur "Publish release"

### Option B : Via Git CLI

```bash
# Créer et pousser le tag
git tag -a v1.0.0 -m "Release v1.0.0 - Initial stable release"
git push origin v1.0.0
```

## Étape 6 : Vérifier la Publication

### 6.1 Vérifier le Workflow GitHub Actions

1. Aller sur votre repository → Actions
2. Vous devriez voir un workflow "Publish PHP SDK to Packagist" en cours
3. Attendre que le workflow soit ✅ (vert)

### 6.2 Vérifier sur Packagist

1. Aller sur [packagist.org/packages/wouaka/sdk](https://packagist.org/packages/wouaka/sdk)
2. Vérifier que la version `1.0.0` apparaît
3. Vérifier que le badge "stable" est vert

### 6.3 Tester l'Installation

```bash
# Créer un projet test
mkdir test-wouaka-sdk
cd test-wouaka-sdk

# Initialiser Composer
composer init --no-interaction

# Installer le SDK
composer require wouaka/sdk

# Vérifier l'installation
composer show wouaka/sdk
```

Si l'installation réussit, le SDK est publié avec succès ! 🎉

## Étape 7 : Mettre à Jour la Documentation

Ajouter le badge Packagist dans `src/sdk/php/README.md` :

```markdown
[![Latest Stable Version](https://poser.pugx.org/wouaka/sdk/v/stable)](https://packagist.org/packages/wouaka/sdk)
[![Total Downloads](https://poser.pugx.org/wouaka/sdk/downloads)](https://packagist.org/packages/wouaka/sdk)
[![License](https://poser.pugx.org/wouaka/sdk/license)](https://packagist.org/packages/wouaka/sdk)
```

## Publier des Versions Futures

Pour publier de nouvelles versions (ex: v1.1.0, v2.0.0) :

1. Faire vos modifications de code
2. Mettre à jour `WouakaClient::VERSION` dans `src/WouakaClient.php`
3. Créer un nouveau tag GitHub (voir Étape 5)
4. Le workflow GitHub Actions publiera automatiquement la nouvelle version

## Tests en Local (Optionnel)

Avant de publier, tester le package localement :

```bash
# Installer les dépendances
composer install

# Exécuter les tests
composer test

# Analyse statique
composer analyse

# Vérifier le code style
composer cs

# Valider le composer.json
composer validate --strict
```

## Troubleshooting

### Erreur : "Package not found"

- Vérifier que le repository GitHub est public
- Vérifier que le webhook Packagist est configuré
- Attendre 5-10 minutes après la soumission initiale

### Erreur : "Invalid composer.json"

```bash
composer validate --strict
```

Corriger les erreurs indiquées.

### Le Workflow GitHub Actions Échoue

- Vérifier que `PACKAGIST_API_TOKEN` est bien configuré dans les secrets GitHub
- Vérifier les logs du workflow dans Actions
- S'assurer que les tests PHPUnit passent

## Resources

- **Packagist** : https://packagist.org/
- **Documentation Composer** : https://getcomposer.org/doc/
- **Documentation Wouaka** : https://docs.wouaka.com
- **Support** : support@wouaka.com
