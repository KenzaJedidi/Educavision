# ===============================================
# Tests API Chapitre - PowerShell Script
# ===============================================

$API_URL = "http://127.0.0.1:8000/api/chapters"
$COURSE_ID = 1
$CHAPTER_ID = 1

Write-Host "========== API CHAPITRE - TESTS ==========" -ForegroundColor Green

# 1. Récupérer les chapitres d'un cours
Write-Host "`n1️⃣ GET /api/chapters/course/{courseId}" -ForegroundColor Cyan
Write-Host "GET $API_URL/course/$COURSE_ID"
$response = Invoke-WebRequest -Uri "$API_URL/course/$COURSE_ID" -Method GET -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 2. Récupérer un chapitre spécifique
Write-Host "`n2️⃣ GET /api/chapters/{id}" -ForegroundColor Cyan
Write-Host "GET $API_URL/$CHAPTER_ID"
$response = Invoke-WebRequest -Uri "$API_URL/$CHAPTER_ID" -Method GET -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 3. Enrichir un chapitre avec IA
Write-Host "`n3️⃣ POST /api/chapters/{id}/enrich" -ForegroundColor Cyan
Write-Host "POST $API_URL/$CHAPTER_ID/enrich"
Write-Host "⏳ Ceci peut prendre 10-15 secondes (appel OpenAI)..." -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri "$API_URL/$CHAPTER_ID/enrich" -Method POST -UseBasicParsing -Headers @{"Content-Type"="application/json"}
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 4. Traduire un chapitre
Write-Host "`n4️⃣ POST /api/chapters/{id}/translate" -ForegroundColor Cyan
Write-Host "POST $API_URL/$CHAPTER_ID/translate"
$body = @{ targetLanguage = "en" } | ConvertTo-Json
$response = Invoke-WebRequest -Uri "$API_URL/$CHAPTER_ID/translate" -Method POST -UseBasicParsing `
    -Headers @{"Content-Type"="application/json"} -Body $body
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 5. Récupérer les langues disponibles
Write-Host "`n5️⃣ GET /api/chapters/languages/available" -ForegroundColor Cyan
Write-Host "GET $API_URL/languages/available"
$response = Invoke-WebRequest -Uri "$API_URL/languages/available" -Method GET -UseBasicParsing
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 6. Réorganiser les chapitres
Write-Host "`n6️⃣ POST /api/chapters/reorder" -ForegroundColor Cyan
Write-Host "POST $API_URL/reorder"
$body = @{
    courseId = 1
    chapterPositions = @(
        @{ chapterId = 1; position = 1 }
    )
} | ConvertTo-Json
$response = Invoke-WebRequest -Uri "$API_URL/reorder" -Method POST -UseBasicParsing `
    -Headers @{"Content-Type"="application/json"} -Body $body
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 7. Publier un chapitre
Write-Host "`n7️⃣ POST /api/chapters/{id}/publish" -ForegroundColor Cyan
Write-Host "POST $API_URL/$CHAPTER_ID/publish"
$response = Invoke-WebRequest -Uri "$API_URL/$CHAPTER_ID/publish" -Method POST -UseBasicParsing `
    -Headers @{"Content-Type"="application/json"}
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

# 8. Sauvegarder un brouillon
Write-Host "`n8️⃣ POST /api/chapters/{id}/draft" -ForegroundColor Cyan
Write-Host "POST $API_URL/$CHAPTER_ID/draft"
$body = @{
    titre = "Nouveau titre"
    description = "Nouvelle description"
} | ConvertTo-Json
$response = Invoke-WebRequest -Uri "$API_URL/$CHAPTER_ID/draft" -Method POST -UseBasicParsing `
    -Headers @{"Content-Type"="application/json"} -Body $body
$response.Content | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host

Write-Host "`n========== TESTS TERMINÉS ==========" -ForegroundColor Green
