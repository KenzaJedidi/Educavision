/**
 * Course Module - Frontend JavaScript Examples
 * Consommer les API REST du module Courses
 */

// ============================================
// CONFIG
// ============================================

const API_BASE_URL = '/cours/api';
const API_TIMEOUT = 10000; // 10 secondes

// ============================================
// Classe Helper pour les appels API
// ============================================

class CourseAPI {
  constructor(baseUrl = API_BASE_URL) {
    this.baseUrl = baseUrl;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const defaultOptions = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      timeout: API_TIMEOUT,
    };

    const config = { ...defaultOptions, ...options };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || `Erreur ${response.status}`);
      }

      return data;
    } catch (error) {
      console.error('Erreur API:', error);
      throw error;
    }
  }

  // Recherche avancée
  async searchCourses(params = {}) {
    const queryString = new URLSearchParams({
      title: params.title || '',
      category: params.category || '',
      keywords: params.keywords || '',
      sort: params.sort || 'date',
      page: params.page || 1,
      limit: params.limit || 12,
    }).toString();

    return this.request(`/search?${queryString}`);
  }

  // Détail cours + ressources
  async getCourse(courseId) {
    return this.request(`/${courseId}`);
  }

  // Générer résumé IA
  async generateSummary(courseId) {
    return this.request(`/${courseId}/generate-summary`, {
      method: 'POST',
    });
  }

  // Générer mots-clés IA
  async generateKeywords(courseId) {
    return this.request(`/${courseId}/generate-keywords`, {
      method: 'POST',
    });
  }

  // Récupérer ressources Wikipedia
  async getResources(courseId) {
    return this.request(`/${courseId}/resources`);
  }

  // Liker un cours
  async likeCourse(courseId) {
    return this.request(`/${courseId}/like`, {
      method: 'POST',
    });
  }

  // Cours populaires
  async getPopularCourses(limit = 6) {
    return this.request(`/popular?limit=${limit}`);
  }

  // Cours récents
  async getLatestCourses(limit = 6) {
    return this.request(`/latest?limit=${limit}`);
  }
}

// Initialiser l'API client
const courseAPI = new CourseAPI();

// ============================================
// EXEMPLES D'UTILISATION
// ============================================

// ========== 1. RECHERCHE AVANCÉE PAGINÉE ==========
async function searchCourses() {
  try {
    const result = await courseAPI.searchCourses({
      title: 'JavaScript',
      category: 'Développement',
      keywords: 'web,async',
      sort: 'popularity',
      page: 1,
      limit: 12,
    });

    console.log('Cours trouvés:', result.data.data);
    console.log('Pagination:', result.data.pagination);
    
    // Afficher les résultats
    displayCourseList(result.data.data);
    displayPagination(result.data.pagination);
  } catch (error) {
    showError('Erreur lors de la recherche');
  }
}

// ========== 2. AFFICHER DÉTAIL COURS + RESSOURCES ==========
async function viewCourseDetails(courseId) {
  try {
    const result = await courseAPI.getCourse(courseId);
    const course = result.data.course;
    const resources = result.data.complementary_resources;

    console.log('Cours:', course);
    console.log('Ressources complémentaires:', resources);

    // Afficher le cours
    displayCourseDetail(course, resources);
  } catch (error) {
    showError('Erreur lors du chargement du cours');
  }
}

// ========== 3. GÉNÉRER RÉSUMÉ IA ==========
async function generateCourseSummary(courseId, buttonElement) {
  try {
    // Désactiver le bouton
    buttonElement.disabled = true;
    buttonElement.textContent = 'Génération en cours...';

    const result = await courseAPI.generateSummary(courseId);

    if (result.success) {
      console.log('Résumé généré:', result.data.summary);
      
      // Afficher le résumé dans le DOM
      const summaryElement = document.querySelector('[data-summary]');
      if (summaryElement) {
        summaryElement.textContent = result.data.summary;
        showSuccess('Résumé IA généré avec succès');
      }
    } else {
      showError(result.error || 'Erreur lors de la génération');
    }
  } catch (error) {
    showError('Erreur lors de la génération du résumé');
  } finally {
    // Réactiver le bouton
    buttonElement.disabled = false;
    buttonElement.textContent = 'Générer Résumé IA';
  }
}

// ========== 4. GÉNÉRER MOTS-CLÉS IA ==========
async function generateCourseKeywords(courseId, buttonElement) {
  try {
    buttonElement.disabled = true;
    buttonElement.textContent = 'Génération en cours...';

    const result = await courseAPI.generateKeywords(courseId);

    if (result.success) {
      console.log('Mots-clés:', result.data.keywords_list);

      // Afficher les mots-clés
      const keywordsContainer = document.querySelector('[data-keywords]');
      if (keywordsContainer) {
        keywordsContainer.innerHTML = result.data.keywords_list
          .map(kw => `<span class="keyword-tag">${kw}</span>`)
          .join('');
        showSuccess('Mots-clés générés avec succès');
      }
    } else {
      showError(result.error || 'Erreur lors de la génération');
    }
  } catch (error) {
    showError('Erreur lors de la génération des mots-clés');
  } finally {
    buttonElement.disabled = false;
    buttonElement.textContent = 'Générer Mots-clés';
  }
}

// ========== 5. AFFICHER RESSOURCES WIKIPEDIA ==========
async function loadComplementaryResources(courseId) {
  try {
    const result = await courseAPI.getResources(courseId);

    if (result.success && result.data.articles.length > 0) {
      console.log('Ressources trouvées:', result.data.articles);
      displayResources(result.data.articles);
    } else {
      console.log('Aucune ressource complémentaire trouvée');
    }
  } catch (error) {
    console.error('Erreur lors du chargement des ressources');
  }
}

// ========== 6. LIKER UN COURS ==========
async function likeCourse(courseId, buttonElement) {
  try {
    await courseAPI.likeCourse(courseId);
    
    // Mettre à jour l'interface
    const likeCount = buttonElement.querySelector('[data-like-count]');
    if (likeCount) {
      likeCount.textContent = parseInt(likeCount.textContent) + 1;
    }
    
    buttonElement.classList.add('liked');
    showSuccess('Cours liké');
  } catch (error) {
    showError('Erreur lors du like');
  }
}

// ========== 7. AFFICHER COURS POPULAIRES ==========
async function displayPopularCourses() {
  try {
    const result = await courseAPI.getPopularCourses(6);
    console.log('Cours populaires:', result.data);
    
    displayCourseCarousel(result.data, 'populaire');
  } catch (error) {
    showError('Erreur lors du chargement des cours populaires');
  }
}

// ========== 8. AFFICHER COURS RÉCENTS ==========
async function displayLatestCourses() {
  try {
    const result = await courseAPI.getLatestCourses(6);
    console.log('Cours récents:', result.data);
    
    displayCourseCarousel(result.data, 'récent');
  } catch (error) {
    showError('Erreur lors du chargement des cours récents');
  }
}

// ============================================
// FONCTIONS D'AFFICHAGE
// ============================================

/**
 * Affiche une liste de cours
 */
function displayCourseList(courses) {
  const container = document.querySelector('[data-courses-list]');
  if (!container) return;

  container.innerHTML = courses
    .map(course => `
      <div class="course-card">
        <img src="${course.image_url}" alt="${course.title}" />
        <h3>${course.title}</h3>
        <p class="category">${course.category}</p>
        <p class="price">${course.price || 'Gratuit'}</p>
        <button onclick="viewCourseDetails(${course.id})">Voir le cours</button>
      </div>
    `)
    .join('');
}

/**
 * Affiche la pagination
 */
function displayPagination(pagination) {
  const container = document.querySelector('[data-pagination]');
  if (!container) return;

  const buttons = [];
  
  // Bouton précédent
  if (pagination.page > 1) {
    buttons.push(
      `<button onclick="searchWithPage(${pagination.page - 1})">← Précédent</button>`
    );
  }

  // Numéros de page
  for (let i = 1; i <= pagination.pages; i++) {
    if (i === pagination.page) {
      buttons.push(`<span class="current">${i}</span>`);
    } else {
      buttons.push(
        `<button onclick="searchWithPage(${i})">${i}</button>`
      );
    }
  }

  // Bouton suivant
  if (pagination.page < pagination.pages) {
    buttons.push(
      `<button onclick="searchWithPage(${pagination.page + 1})">Suivant →</button>`
    );
  }

  container.innerHTML = buttons.join('');
}

/**
 * Affiche le détail d'un cours
 */
function displayCourseDetail(course, resources = []) {
  const container = document.querySelector('[data-course-detail]');
  if (!container) return;

  let resourcesHTML = '';
  if (resources.length > 0) {
    resourcesHTML = `
      <div class="resources-section">
        <h4>Ressources complémentaires</h4>
        <ul>
          ${resources.map(r => `
            <li>
              <a href="${r.url}" target="_blank" rel="noopener">${r.title}</a>
              <p>${r.summary}</p>
            </li>
          `).join('')}
        </ul>
      </div>
    `;
  }

  container.innerHTML = `
    <div class="course-header">
      <img src="${course.image_url}" alt="${course.title}" />
      <h1>${course.title}</h1>
      <p class="category">${course.category}</p>
    </div>
    
    <div class="course-body">
      <div class="course-summary">
        <h2>Résumé du cours</h2>
        <p>${course.summary || course.description}</p>
      </div>
      
      ${resourcesHTML}
      
      <div class="course-meta">
        <span>👁 ${course.views} vues</span>
        <span>❤️ ${course.likes} likes</span>
      </div>
    </div>
  `;
}

/**
 * Affiche les ressources Wikipedia
 */
function displayResources(articles) {
  const container = document.querySelector('[data-resources]');
  if (!container) return;

  container.innerHTML = `
    <h3>Ressources complémentaires</h3>
    <div class="resources-grid">
      ${articles.map(article => `
        <div class="resource-card">
          <h4><a href="${article.url}" target="_blank">${article.title}</a></h4>
          <p>${article.summary}</p>
          <small>Source: ${article.source} • Pertinence: ${(article.relevance_score * 100).toFixed(0)}%</small>
        </div>
      `).join('')}
    </div>
  `;
}

/**
 * Affiche un carrousel de cours
 */
function displayCourseCarousel(courses, type) {
  const container = document.querySelector(`[data-${type}-courses]`);
  if (!container) return;

  container.innerHTML = `
    <div class="carousel">
      ${courses.map(course => `
        <div class="carousel-item">
          <img src="${course.image_url}" alt="${course.title}" />
          <h3>${course.title}</h3>
          <p>${course.category}</p>
          <button onclick="viewCourseDetails(${course.id})">Voir</button>
        </div>
      `).join('')}
    </div>
  `;
}

// ============================================
// NOTIFICATIONS
// ============================================

function showSuccess(message) {
  const notification = document.createElement('div');
  notification.className = 'notification success';
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => notification.remove(), 3000);
}

function showError(message) {
  const notification = document.createElement('div');
  notification.className = 'notification error';
  notification.textContent = `❌ ${message}`;
  document.body.appendChild(notification);
  
  setTimeout(() => notification.remove(), 5000);
}

// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener('DOMContentLoaded', () => {
  // Charger les cours populaires au chargement
  displayPopularCourses();
  displayLatestCourses();

  // Recherche au changement des filtres
  const searchForm = document.querySelector('[data-search-form]');
  if (searchForm) {
    searchForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = new FormData(searchForm);
      const params = {
        title: formData.get('title'),
        category: formData.get('category'),
        keywords: formData.get('keywords'),
        sort: formData.get('sort') || 'date',
      };

      await searchCourses(params);
    });
  }
});

// Export pour utilisation en module
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { CourseAPI, courseAPI };
}
