# Configuration DeepSeek pour la génération de Quiz

## Configuration

1. **Obtenir une clé API DeepSeek**
   - Rendez-vous sur [https://platform.deepseek.com](https://platform.deepseek.com)
   - Créez un compte et demandez une clé API

2. **Configurer la clé dans le projet**
   - Ouvrez le fichier `.env` (ou `.env.local` pour une config locale)
   - Ajoutez ou modifiez la ligne :
   ```
   DEEPSEEK_API_KEY=votre-clé-api-ici
   ```

## Comportement

- **Si `DEEPSEEK_API_KEY` est configurée** : Le générateur de quiz utilise l’API DeepSeek pour créer des questions à partir du texte. Le nombre de questions demandé par le professeur est respecté.
- **Si la clé n’est pas configurée** : Le système utilise un mode heuristique (moins précis) pour générer les questions.

## Utilisation

1. Aller sur `/quiz/ai/create`
2. Remplir le formulaire (titre, texte source, nombre de questions, difficulté, etc.)
3. Cliquer sur « Générer le Quiz avec l’IA »
4. Prévisualiser puis sauvegarder le quiz

Le nombre de questions indiqué dans le formulaire sera respecté lors de la génération via DeepSeek.
