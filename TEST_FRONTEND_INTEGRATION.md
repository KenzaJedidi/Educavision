# 🧪 TEST DE L'INTÉGRATION FRONTEND

## ✅ CHECKLIST DE VÉRIFICATION

### 1️⃣ Page Detail (`/cours/{id}`)
**URL:** `http://localhost/cours/1`

#### Visual Checks:
- [ ] Titre du cours affiché
- [ ] Description visible
- [ ] Image du cours chargée
- [ ] Prix affiché

#### AI Features:
- [ ] Section "Résumé IA" visible
  - Si `summary` existe → Texte affiché
  - Si vide → Bouton "Générer Résumé IA"
- [ ] Section "Mots-clés" visible
  - Si `keywords` existent → Badges affichés (séparés par virgules)
  - Si vides → Bouton "Générer Mots-clés"
- [ ] Section "Ressources Wikipedia" affichée
  - Cartes avec titre, résumé, lien

#### Interactive Tests:
```javascript
// Test 1: Générer le résumé
1. Clic sur "Générer Résumé IA"
2. Attendre la réponse API (3-5 sec)
3. Page se recharge automatiquement
4. Vérifier que le résumé s'affiche maintenant
```

```javascript
// Test 2: Générer les mots-clés
1. Clic sur "Générer Mots-clés"
2. Attendre la réponse API (2-3 sec)
3. Page se recharge automatiquement
4. Vérifier que les mots-clés s'affichent
```

```javascript
// Test 3: Aimer le cours
1. Clic sur "❤️ Aimer"
2. Le compteur augmente de 1
3. Vérifier la réaction instantanée
```

---

### 2️⃣ Page List (`/cours/`)
**URL:** `http://localhost/cours/`

#### Initial Load:
- [ ] Formulaire recherche avancée visible en haut
  - Champ "Titre des cours" (input text)
  - Dropdown "Catégorie"
  - Champ "Mots-clés" (input text)
  - Dropdown "Tri"
  - Bouton "Recherche Avancée"
- [ ] Résultats affichés immédiatement (12 par page)
- [ ] Cartes de cours formatées correctement
  - Image visible
  - Catégorie en badge
  - Titre cliquable
  - Description tronquée
  - Prix/Gratuit badge
  - Date création
  - Bouton "Voir le cours"

#### Search Tests:
```
Test 1: Rechercher par titre
1. Taper "Laravel" dans le champ Titre
2. Attendre 500ms (debounce)
3. Vérifier les résultats filtrés
4. Confirmer que seuls les cours contenant "Laravel" s'affichent
```

```
Test 2: Filtrer par catégorie
1. Sélectionner une catégorie du dropdown
2. Vérifier immédiatement le filtrage
3. Confirmer les résultats affichés sont de cette catégorie
```

```
Test 3: Rechercher par mots-clés
1. Taper "php, web" dans le champ Mots-clés
2. Attendre 500ms
3. Vérifier les résultats avec ces mots-clés
```

```
Test 4: Trier les résultats
1. Sélectionner "Populaires" du dropdown Tri
2. Vérifier l'ordre des résultats change
3. Tester tous les tris:
   - Plus récents
   - Populaires
   - Les plus vus
   - Prix croissant
   - Prix décroissant
```

#### Pagination Tests:
```
Test 5: Navigation pagination
1. Si > 12 résultats, vérifier pagination visible
2. Cliquer sur "Page 2"
3. Vérifier page 2 résultats s'affichent
4. Cliquer sur "Page 3"
5. Cliquer "Précédent" → Retour à page 2
6. Cliquer "Suivant" → Avance à page 3
7. Vérifier le scroll automatique vers les résultats
```

```
Test 6: Pagination edge cases
1. Vérifier affichage max 5 pages (même si 10 pages totales)
2. Vérifier ".." pour pages masquées
3. Vérifier liens "1" et "dernière" toujours visibles
```

#### User Experience:
- [ ] Recherche en temps réel (sans cliquer bouton recherche)
- [ ] Spinner de chargement visible pendant recherche
- [ ] Message "Aucun cours trouvé" si aucun résultat
- [ ] Message d'erreur en cas de problème API

---

### 3️⃣ Integration Tests

#### API Data Format:
```bash
# Test dans la console du navigateur:
fetch('/cours/api/search?title=Laravel&page=1&limit=12')
  .then(r => r.json())
  .then(d => console.log(d))
  
# Vérifier la structure:
# {
#   "success": true,
#   "data": {
#     "data": [...courses...],
#     "pagination": {...}
#   }
# }
```

#### Link Functionality:
- [ ] Clic sur une carte de cours → Redirection vers `/cours/{id}`
- [ ] Page détail charge les données correctes du cours
- [ ] Bouton "Voir le cours" fonctionne

---

### 4️⃣ Error Scenarios

#### Network Errors:
```
Test 1: API Offline
1. Simuler offline (Dev Tools → Network → Offline)
2. Rechercher
3. Vérifier message d'erreur:
   "Erreur lors du chargement: Failed to fetch"
```

#### Missing Data:
```
Test 2: Cours sans image
1. Trouver un cours sans imageUrl
2. Vérifier image par défaut chargée
```

```
Test 3: Cours sans résumé IA
1. Vérifier bouton "Générer" visible
2. Cliquer et tester génération
```

---

### 5️⃣ Performance Tests

#### Debounce Effectiveness:
```javascript
// Console du navigateur:
// Vérifier dans "Network" tab:
// - Taper rapidement "laravel" (10 caractères)
// - Devrait faire 1 requête (pas 10!)
```

#### Page Load Time:
- [ ] Page `/cours/` charge en < 2 secondes
- [ ] Page `/cours/1` charge en < 2 secondes
- [ ] Pagination < 1 seconde
- [ ] Génération résumé IA 3-5 secondes (API OpenAI)

---

## 🔧 DEBUGGING GUIDE

### Problem: "Aucun cours" affichés
```bash
# Vérifier:
1. Courses existent en DB: php bin/console doctrine:query:sql "SELECT COUNT(*) FROM course"
2. Status de cours = 1 (actif): SELECT COUNT(*) FROM course WHERE status = 1
3. Cache: php bin/console cache:clear
```

### Problem: Formulaire recherche invisible
```bash
# Vérifier:
1. Éléments HTML présents (F12 → Éléments)
2. CSS chargé: Fondos de couleur appliqués
3. JavaScript chargé: Console → pas d'erreur red
```

### Problem: API retourne 404
```bash
# Vérifier:
1. Route définie: php bin/console debug:router | grep cours/api
2. Controller accessible
3. Request parameters corrects
```

### Problem: Résumé/Mots-clés non générés
```bash
# Vérifier:
1. OPENAI_API_KEY configurée: echo $OPENAI_API_KEY
2. Clé API valide (test curl):
   curl -X POST https://api.openai.com/v1/chat/completions \
     -H "Authorization: Bearer $OPENAI_API_KEY"
3. Vérifier logs: tail -f var/log/dev.log | grep "Course"
```

---

## 📊 EXPECTED BEHAVIORS

### Success Scenarios:
| Action | Expected Result | Time |
|--------|-----------------|------|
| Load `/cours/` | 12 courses affichés | < 2s |
| Search "Laravel" | Résultats filtrés en temps réel | < 1s |
| Click page 2 | 12 nouveaux courses | < 1s |
| Générer résumé | Page recharge avec résumé | 3-5s |
| Générer mots-clés | Page recharge avec tags | 2-3s |
| Click "Voir cours" | Page détail charge | < 2s |
| Click ❤️ | Compteur augmente | Immédiat |

### Error Scenarios:
| Scenario | Expected Result |
|----------|-----------------|
| API offline | Message d'erreur |
| Pas de résultats | Message "Aucun cours trouvé" |
| Paramètres invalides | Résultats filtrés correctly |
| Pagination > total | Dernière page affichée |

---

## ✅ FINAL SIGN-OFF

Once all tests pass:

- [ ] Page list recherche fonctionne
- [ ] Page detail affichage IA correct
- [ ] API endpoints répondent correctement
- [ ] Pagination fonctionne
- [ ] Erreurs gérées proprement
- [ ] Performance acceptable
- [ ] Design responsive (mobile ok)

**Validation:** Move to production ✅
