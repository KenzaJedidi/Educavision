#!/bin/bash
# ============================================
# Course Module API - Exemples de TEST CURL
# ============================================
# Adapter BASE_URL selon votre environnement
BASE_URL="http://localhost:8000"
COURSE_ID=1

echo "🚀 Test des Endpoints Course Module API"
echo "=========================================="

# ========== 1. RECHERCHE AVANCÉE PAGINÉE ==========
echo ""
echo "1️⃣ RECHERCHE AVANCÉE PAGINÉE"
echo "Test avec filtres multiples et tri par popularité"
curl -X GET "$BASE_URL/cours/api/search?title=JavaScript&category=Développement&keywords=web,backend&sort=popularity&page=1&limit=12" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 2. DÉTAIL COURS + RESSOURCES WIKIPEDIA ==========
echo ""
echo ""
echo "2️⃣ DÉTAIL COURS + RESSOURCES WIKIPEDIA"
curl -X GET "$BASE_URL/cours/api/$COURSE_ID" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 3. GÉNÉRER RÉSUMÉ IA ==========
echo ""
echo ""
echo "3️⃣ GÉNÉRER RÉSUMÉ IA"
curl -X POST "$BASE_URL/cours/api/$COURSE_ID/generate-summary" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 4. GÉNÉRER MOTS-CLÉS IA ==========
echo ""
echo ""
echo "4️⃣ GÉNÉRER MOTS-CLÉS IA"
curl -X POST "$BASE_URL/cours/api/$COURSE_ID/generate-keywords" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 5. RÉCUPÉRER RESSOURCES WIKIPEDIA ==========
echo ""
echo ""
echo "5️⃣ RÉCUPÉRER RESSOURCES WIKIPEDIA"
curl -X GET "$BASE_URL/cours/api/$COURSE_ID/resources" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 6. INCRÉMENTER LIKES ==========
echo ""
echo ""
echo "6️⃣ INCRÉMENTER LIKES"
curl -X POST "$BASE_URL/cours/api/$COURSE_ID/like" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 7. COURS POPULAIRES ==========
echo ""
echo ""
echo "7️⃣ COURS POPULAIRES"
curl -X GET "$BASE_URL/cours/api/popular?limit=6" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 8. COURS RÉCENTS ==========
echo ""
echo ""
echo "8️⃣ COURS RÉCENTS"
curl -X GET "$BASE_URL/cours/api/latest?limit=6" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 9. RECHERCHE PAR TITRE SEUL ==========
echo ""
echo ""
echo "9️⃣ RECHERCHE PAR TITRE SEUL"
curl -X GET "$BASE_URL/cours/api/search?title=Python&page=1&limit=10" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

# ========== 10. RECHERCHE PAR CATÉGORIE ==========
echo ""
echo ""
echo "🔟 RECHERCHE PAR CATÉGORIE"
curl -X GET "$BASE_URL/cours/api/search?category=Science&sort=views&page=1&limit=12" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -v

echo ""
echo ""
echo "✅ Tests terminés!"
