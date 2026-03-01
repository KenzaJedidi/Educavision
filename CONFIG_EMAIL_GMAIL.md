# Recevoir les emails de réinitialisation - Configuration Gmail

## Problème
Par défaut, `MAILER_DSN=smtp://localhost:1025` envoie vers MailHog (outil de dev). **Les emails ne partent pas vers votre vraie boîte mail.**

## Solution : utiliser Gmail

### Étape 1 : Créer un mot de passe d'application Google
1. Allez sur https://myaccount.google.com/security
2. Activez la **validation en 2 étapes** si ce n'est pas déjà fait
3. Cherchez **"Mots de passe des applications"**
4. Créez un mot de passe pour "Courrier" / "Autre"
5. Copiez le mot de passe (format xxxx-xxxx-xxxx-xxxx)

### Étape 2 : Créer le fichier .env.local
À la racine du projet, créez un fichier **`.env.local`** avec :

```env
###> Configuration Gmail pour envoi d'emails ###
MAILER_DSN=smtps://VOTRE_EMAIL@gmail.com:VOTRE_MOT_DE_PASSE_APP@smtp.gmail.com:465
MAILER_FROM_ADDRESS=VOTRE_EMAIL@gmail.com
MAILER_FROM_NAME=EducaVision
###< Configuration Gmail ###
```

**Remplacez :**
- `VOTRE_EMAIL@gmail.com` → votre adresse Gmail
- `VOTRE_MOT_DE_PASSE_APP` → le mot de passe d'application (sans les tirets si besoin)

### Étape 3 : Vider le cache
```bash
php bin/console cache:clear
```

### Étape 4 : Tester
Allez sur `/mot-de-passe-oublie`, entrez un email de compte existant. Vous devriez recevoir l'email.

> **Note :** Ne commitez jamais `.env.local` (il est dans .gitignore). Ce fichier contient vos identifiants.
