// TinyMCE integration for chapter content textarea
document.addEventListener('DOMContentLoaded', function() {
    // Check if TinyMCE is available and the textarea exists
    if (typeof tinymce !== 'undefined' && document.getElementById('chapter_content')) {
        tinymce.init({
            selector: '#chapter_content',
            height: 400,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            language: 'fr_FR',
            branding: false,
            promotion: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            entity_encoding: 'raw',
            // Auto-save functionality
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m'
        });
    }
});

// Translation API integration
document.addEventListener('DOMContentLoaded', function() {
    // Add translate button to chapter form if user has admin rights
    const chapterForm = document.querySelector('form[name="chapter"]');
    if (chapterForm && document.getElementById('chapter_content')) {
        const translateButton = document.createElement('button');
        translateButton.type = 'button';
        translateButton.className = 'btn btn-info btn-sm mt-2';
        translateButton.innerHTML = '<i class="fa fa-language"></i> Traduire';
        translateButton.style.marginLeft = '10px';
        
        translateButton.addEventListener('click', function() {
            const content = tinymce.get('chapter_content').getContent();
            if (content.trim() === '') {
                alert('Veuillez d\'abord ajouter du contenu à traduire.');
                return;
            }
            
            // Show translation modal or prompt
            const targetLang = prompt('Langue cible (ex: en, es, de, it):');
            if (targetLang) {
                translateContent(content, targetLang);
            }
        });
        
        // Insert button after the textarea label
        const contentLabel = document.querySelector('label[for="chapter_content"]');
        if (contentLabel) {
            contentLabel.parentNode.insertBefore(translateButton, contentLabel.nextSibling);
        }
    }
});

function translateContent(text, targetLang) {
    fetch('/api/translation/translate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            text: text,
            targetLang: targetLang,
            sourceLang: 'auto'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show confirmation dialog
            if (confirm('Traduction trouvée. Voulez-vous remplacer le contenu actuel ?')) {
                tinymce.get('chapter_content').setContent(data.translatedText);
            } else {
                // Show translation in a modal for review
                showTranslationModal(data.translatedText);
            }
        } else {
            alert('Erreur de traduction: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Translation error:', error);
        alert('Erreur lors de la traduction. Veuillez réessayer.');
    });
}

function showTranslationModal(translatedText) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('translationModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'translationModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Traduction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Texte traduit:</label>
                            <textarea id="translatedContent" class="form-control" rows="10">${translatedText}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="applyTranslation()">Appliquer</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        document.getElementById('translatedContent').value = translatedText;
    }
    
    // Show modal (using Bootstrap if available, otherwise simple display)
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        modal.style.display = 'block';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modal.style.zIndex = '9999';
    }
}

function applyTranslation() {
    const translatedContent = document.getElementById('translatedContent').value;
    tinymce.get('chapter_content').setContent(translatedContent);
    
    // Close modal
    const modal = document.getElementById('translationModal');
    if (typeof bootstrap !== 'undefined') {
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
    } else {
        modal.style.display = 'none';
    }
}
