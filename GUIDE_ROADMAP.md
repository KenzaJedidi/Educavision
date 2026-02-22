## 🎯 Guide Rapide - Roadmap Visuelle Formation

### 📋 Ce qui a été ajouté

Une roadmap visuellement attrayante sur chaque page de détail formation avec 3 étapes :

1.  **Avant la formation** - Affiche les prérequis
2.  **Pendant la formation** - Affiche les compétences acquises  
3.  **Après la formation** - Affiche les débouchés professionnels

Plus un **badge dynamique** indiquant la difficulté globale de la formation.

---

### 🚀 Comment Utiliser dans l'Admin

#### Étape 1: Créer ou Éditer une Formation

1. Accédez à: **Admin → Formation → Nouvelle Formation** ou **Éditer**
2. Remplissez les champs existants (nom, description, duree, niveau)
3. Remplissez **"Débouchés Professionnels"** (nouveau champ)
4. Cliquez sur **Enregistrer**

#### Étape 2: Configurer les Prérequis

Les prérequis se gèrent depuis une table séparée et apparaissent automatiquement sur la roadmap.

---

### 🎨 Comment ça s'affiche Front

Quand un utilisateur consulte une formation, il voit:

```
┌─────────────────────────────────────────┐
│  🛤 Votre parcours de formation        │
├─────────────────────────────────────────┤
│                                         │
│ ● Avant la formation                    │
│   ✓ Prérequis 1                        │
│   ✓ Prérequis 2                        │
│                                         │
│ ● Pendant la formation                  │
│   • Compétence 1                        │
│   • Compétence 2                        │
│                                         │
│ ● Après la formation                    │
│   • Débouché 1                          │
│   • Débouché 2                          │
│                                         │
│ 🟢 Accessible facilement                │
└─────────────────────────────────────────┘
```

---

### 🎯 Badge Dynamique

Le badge se calcule automatiquement selon le nombre de prérequis:

| Prérequis | Badge | Signification |
|-----------|-------|---------------|
| 0-2       | 🟢    | Accessible facilement |
| 3-5       | 🟡    | Accessible avec effort |
| 6+        | 🔴    | Formation exigeante |

---

### 🔧 Détails Techniques

#### Fichiers Clés:

- **Entité**: `src/Entity/Formation.php` - Champ `debouches`
- **Template Frontend**: `templates/front/includes/roadmap.html.twig`
- **Template Admin**: `templates/admin/formation/new.html.twig` et `edit.html.twig`
- **Styles**: `assets/css/roadmap.css`
- **Migration**: `migrations/Version20260218150000.php`

#### Base de Données:

```sql
ALTER TABLE formation ADD debouches LONGTEXT DEFAULT NULL;
```

---

### 📱 Responsive

La roadmap s'adapte automatiquement à tous les écrans:
- **Desktop** ✓ Timeline verticale complète
- **Tablet** ✓ Layout adapté
- **Mobile** ✓ Colonne unique optimisée

---

### ✅ Tests

Pour tester localement:

1. Allez dans l'admin et éditez une formation
2. Remplissez le nouveau champ "Débouchés Professionnels"
3. Enregistrez
4. Consultez la page détail formation
5. La roadmap doit apparaître avec tous les éléments

---

### 💡 Astuces

- **Formatage rich text**: Les champs compétences et débouchés acceptent l'HTML
- **Prérequis triés**: S'affichent dans l'ordre défini (colonne `ordre`)
- **Affichage conditionnel**: La roadmap ne s'affiche que s'il y a du contenu
- **Messages informatifs**: Si aucune donnée, affiche un message

---

### 🎨 Personnalisation CSS

Si vous voulez personnaliser les couleurs ou polices, éditez `assets/css/roadmap.css`:

```css
/* Couleurs des étapes */
.stage-badge-before { color: #28a745; } /* Vert */
.stage-badge-during { color: #ffc107; } /* Jaune */
.stage-badge-after { color: #17a2b8; }  /* Bleu */

/* Badge de difficulté */
.difficulty-easy { background: #d4edda; } /* Vert clair */
.difficulty-medium { background: #fff3cd; } /* Jaune clair */
.difficulty-hard { background: #f8d7da; } /* Rouge clair */
```

---

### 🆘 Troubleshooting

| Problème | Solution |
|----------|----------|
| Roadmap ne s'affiche pas | Vérifiez que le CSS est chargé (cache) |
| Prérequis en mauvais ordre | Vérifiez la colonne `ordre` dans la table |
| Les débouchés ne s'enregistrent pas | Le champ a peut-être besoin de la migration |
| Design cassé | Videz le cache browser (Ctrl+Shift+Del) |

---

### 📞 Support

Toutes les modifications sont non-intrusive et compatibles avec le code existant.
Aucune dépendance externe ajoutée.

✅ Production Ready - Prêt à l'emploi!
