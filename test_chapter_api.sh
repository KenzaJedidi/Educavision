#!/bin/bash

# ===============================================
# Tests API Chapitre - Scripts curl
# ===============================================

API_URL="http://127.0.0.1:8000/api/chapters"
COURSE_ID=1
CHAPTER_ID=1

echo "========== API CHAPITRE - TESTS =========="

# 1. Récupérer les chapitres d'un cours
echo -e "\n1️⃣ GET /api/chapters/course/{courseId}"
echo "curl -X GET $API_URL/course/$COURSE_ID"
curl -s -X GET "$API_URL/course/$COURSE_ID" | jq '.'

# 2. Récupérer un chapitre spécifique
echo -e "\n\n2️⃣ GET /api/chapters/{id}"
echo "curl -X GET $API_URL/$CHAPTER_ID"
curl -s -X GET "$API_URL/$CHAPTER_ID" | jq '.'

# 3. Enrichir un chapitre avec IA
echo -e "\n\n3️⃣ POST /api/chapters/{id}/enrich"
echo "curl -X POST $API_URL/$CHAPTER_ID/enrich"
echo "⏳ Ceci peut prendre 10-15 secondes (appel OpenAI)..."
curl -s -X POST "$API_URL/$CHAPTER_ID/enrich" \
  -H "Content-Type: application/json" | jq '.'

# 4. Traduire un chapitre
echo -e "\n\n4️⃣ POST /api/chapters/{id}/translate"
echo "curl -X POST $API_URL/$CHAPTER_ID/translate"
curl -s -X POST "$API_URL/$CHAPTER_ID/translate" \
  -H "Content-Type: application/json" \
  -d '{"targetLanguage":"en"}' | jq '.'

# 5. Récupérer les langues disponibles
echo -e "\n\n5️⃣ GET /api/chapters/languages/available"
echo "curl -X GET $API_URL/languages/available"
curl -s -X GET "$API_URL/languages/available" | jq '.'

# 6. Réorganiser les chapitres
echo -e "\n\n6️⃣ POST /api/chapters/reorder"
echo "curl -X POST $API_URL/reorder"
curl -s -X POST "$API_URL/reorder" \
  -H "Content-Type: application/json" \
  -d '{
    "courseId": 1,
    "chapterPositions": [
      {"chapterId": 1, "position": 1}
    ]
  }' | jq '.'

# 7. Publier un chapitre
echo -e "\n\n7️⃣ POST /api/chapters/{id}/publish"
echo "curl -X POST $API_URL/$CHAPTER_ID/publish"
curl -s -X POST "$API_URL/$CHAPTER_ID/publish" \
  -H "Content-Type: application/json" | jq '.'

# 8. Sauvegarder un brouillon
echo -e "\n\n8️⃣ POST /api/chapters/{id}/draft"
echo "curl -X POST $API_URL/$CHAPTER_ID/draft"
curl -s -X POST "$API_URL/$CHAPTER_ID/draft" \
  -H "Content-Type: application/json" \
  -d '{
    "titre": "Nouveau titre",
    "description": "Nouvelle description"
  }' | jq '.'

# 9. Supprimer un chapitre (optionnel - à décommenter)
# echo -e "\n\n9️⃣ DELETE /api/chapters/{id}"
# echo "curl -X DELETE $API_URL/$CHAPTER_ID"
# curl -s -X DELETE "$API_URL/$CHAPTER_ID" \
#   -H "Content-Type: application/json" | jq '.'

echo -e "\n\n========== TESTS TERMINÉS =========="
