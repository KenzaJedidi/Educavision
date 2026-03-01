# Configuration de la clé API Unsplash

## Étape 1 : Obtenir une clé API Unsplash

1. Allez sur https://unsplash.com/developers
2. Créez un compte ou connectez-vous
3. Cliquez sur "New Application"
4. Remplissez le formulaire :
   - **Name**: EducaVision Course Module
   - **Description**: Gestion des cours avec images automatiques
   - **Usage**: Non-commercial (pour un projet éducatif)
5. Acceptez les termes d'utilisation
6. Copiez la **Access Key** qui vous sera fournie

## Étape 2 : Configurer la clé dans le projet

### Option A : Modifier directement le fichier .env

Ouvrez le fichier `.env` à la racine du projet et remplacez :
```
UNSPLASH_ACCESS_KEY=your_unsplash_access_key_here
```

Par :
```
UNSPLASH_ACCESS_KEY=votre_clé_access_unsplash_ici
```

### Option B : Utiliser les variables d'environnement

Ajoutez la variable d'environnement à votre système :
```bash
export UNSPLASH_ACCESS_KEY=votre_clé_access_unsplash_ici
```

## Étape 3 : Redémarrer le serveur

Après avoir configuré la clé, videz le cache et redémarrez le serveur :

```bash
php bin/console cache:clear
# Redémarrez votre serveur de développement
```

## Étape 4 : Vérifier la configuration

Visitez `http://127.0.0.1:8000/admin/course/` - l'erreur devrait avoir disparu.

## Fonctionnalités disponibles avec la clé configurée

- ✅ **Images automatiques** : Récupération d'images basées sur le titre du cours
- ✅ **Recherche d'images** : Recherche avancée dans la galerie Unsplash
- ✅ **Téléchargement automatique** : Sauvegarde locale des images
- ✅ **Crédits photographes** : Respect des licences Unsplash

## Alternative : Mode sans clé API

Si vous ne souhaitez pas configurer de clé Unsplash :

1. Le système fonctionnera en mode dégradé
2. Les fonctionnalités d'images seront désactivées
3. Vous pourrez toujours télécharger manuellement les images
4. Les autres fonctionnalités (scoring, recommandation, Wikipedia) resteront actives

## Support

- Documentation Unsplash : https://unsplash.com/documentation
- Limites de l'API : 50 requêtes/heure (gratuit)
- Pour une utilisation intensive, envisagez un plan payant

## Sécurité

⚠️ **Important** : Ne commitez jamais votre clé API dans un dépôt Git !
Utilisez toujours les variables d'environnement ou le fichier `.env.local`.
