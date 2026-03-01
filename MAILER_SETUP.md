# Configuration Mailer + SMTP - Récupération mot de passe

## Fonctionnement

1. **Mot de passe oublié** : `/mot-de-passe-oublie`
2. L'utilisateur entre son email
3. Un lien de réinitialisation est envoyé par email (valide 24h selon le message)
4. Clic sur le lien → formulaire nouveau mot de passe

## Configuration rapide

### Développement (MailHog)
```env
MAILER_DSN=smtp://localhost:1025
MAILER_FROM_ADDRESS=no-reply@educavision.com
MAILER_FROM_NAME=EducaVision
```
Lancez [MailHog](https://github.com/mailhog/MailHog) ou [Mailpit](https://github.com/axllent/mailpit) pour capturer les emails localement.

### Gmail
1. Activez la validation en 2 étapes sur votre compte Google
2. Créez un **mot de passe d'application** : [Google Account](https://myaccount.google.com/apppasswords)
3. Dans `.env.local` :
```env
MAILER_DSN=smtps://votre@email.com:xxxx-xxxx-xxxx-xxxx@smtp.gmail.com:465
MAILER_FROM_ADDRESS=votre@email.com
MAILER_FROM_NAME=EducaVision
```

### SMTP classique (OVH, etc.)
```env
MAILER_DSN=smtp://login:motdepasse@smtp.votredomaine.com:587
MAILER_FROM_ADDRESS=noreply@votredomaine.com
MAILER_FROM_NAME=EducaVision
```

## Fichiers concernés

- `.env` / `.env.local` : variables MAILER_*
- `.env.mailer.example` : exemples de configuration
- `config/packages/mailer.yaml` : DSN du framework
- `src/Service/EmailService.php` : envoi des emails
- `src/Controller/SecurityController.php` : logique mot de passe oublié

## Dépannage

- **Email non reçu** : Vérifiez `var/log/dev.log` (erreur "Erreur envoi email récupération mot de passe")
- **Gmail bloque** : Utilisez un mot de passe d'application, pas votre mot de passe habituel
- **Lien invalide** : Vérifiez que l'URL du site est correcte (proxy, HTTPS)
