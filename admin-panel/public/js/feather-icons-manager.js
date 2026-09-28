/**
 * Gestionnaire des icônes Feather
 * Assure le chargement et l'affichage correct des icônes
 */

class FeatherIconsManager {
    constructor() {
        this.isLoaded = false;
        this.retryCount = 0;
        this.maxRetries = 3;
        this.init();
    }

    init() {
        // Vérifier si Feather est déjà chargé
        if (typeof feather !== 'undefined') {
            this.isLoaded = true;
            this.replaceIcons();
        } else {
            // Attendre que la page soit chargée
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.checkAndLoad());
            } else {
                this.checkAndLoad();
            }
        }

        // Écouter les changements de contenu dynamique
        this.observeContentChanges();
    }

    checkAndLoad() {
        if (typeof feather !== 'undefined') {
            this.isLoaded = true;
            this.replaceIcons();
        } else {
            this.loadFeatherIcons();
        }
    }

    loadFeatherIcons() {
        if (this.retryCount >= this.maxRetries) {
            console.error('Impossible de charger Feather Icons après', this.maxRetries, 'tentatives');
            this.showFallbackIcons();
            return;
        }

        this.retryCount++;
        console.log('Tentative de chargement de Feather Icons...', this.retryCount);

        // Essayer de charger depuis le CDN principal
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/feather-icons@4.29.1/dist/feather.min.js';
        script.onload = () => {
            if (typeof feather !== 'undefined') {
                this.isLoaded = true;
                this.replaceIcons();
                console.log('Feather Icons chargé avec succès');
            } else {
                this.loadFeatherIcons();
            }
        };
        script.onerror = () => {
            // Essayer le CDN alternatif
            const altScript = document.createElement('script');
            altScript.src = 'https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js';
            altScript.onload = () => {
                if (typeof feather !== 'undefined') {
                    this.isLoaded = true;
                    this.replaceIcons();
                    console.log('Feather Icons chargé depuis le CDN alternatif');
                } else {
                    this.loadFeatherIcons();
                }
            };
            altScript.onerror = () => {
                this.loadFeatherIcons();
            };
            document.head.appendChild(altScript);
        };
        document.head.appendChild(script);
    }

    replaceIcons() {
        if (!this.isLoaded) return;

        try {
            // Remplacer toutes les icônes avec des tailles appropriées
            feather.replace({
                width: 16,
                height: 16,
                'stroke-width': 2
            });

            // Remplacer spécifiquement les icônes des headers de navigation
            const headerIcons = document.querySelectorAll('.navigation-header i[data-feather]');
            headerIcons.forEach(icon => {
                const iconName = icon.getAttribute('data-feather');
                if (iconName && feather.icons[iconName]) {
                    icon.innerHTML = feather.icons[iconName].toSvg({
                        width: 14,
                        height: 14,
                        'stroke-width': 2
                    });
                }
            });

            // Réinitialiser les icônes dans les éléments dynamiques
            this.replaceDynamicIcons();

            console.log('Icônes du menu mises à jour avec succès');
        } catch (error) {
            console.error('Erreur lors du remplacement des icônes:', error);
        }
    }

    replaceDynamicIcons() {
        // Trouver tous les éléments avec data-feather qui n'ont pas encore d'icône
        const iconElements = document.querySelectorAll('[data-feather]:not([data-feather=""])');

        iconElements.forEach(element => {
            if (!element.querySelector('svg')) {
                try {
                    const iconName = element.getAttribute('data-feather');
                    if (iconName && feather.icons[iconName]) {
                        element.innerHTML = feather.icons[iconName].toSvg({
                            width: 14,
                            height: 14,
                            'stroke-width': 2
                        });
                    }
                } catch (error) {
                    console.error('Erreur lors du remplacement de l\'icône:', error);
                }
            }
        });
    }

    showFallbackIcons() {
        // Afficher des icônes de fallback pour les éléments avec data-feather
        const iconElements = document.querySelectorAll('[data-feather]:not([data-feather=""])');

        iconElements.forEach(element => {
            const iconName = element.getAttribute('data-feather');
            if (iconName) {
                // Créer une icône de fallback simple
                element.innerHTML = `<span class="fallback-icon" title="${iconName}">⚡</span>`;
                element.classList.add('feather-fallback');
            }
        });
    }

    observeContentChanges() {
        // Observer les changements de contenu pour réinitialiser les icônes
        if (window.MutationObserver) {
            const observer = new MutationObserver((mutations) => {
                let shouldReplace = false;

                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                if (node.hasAttribute && node.hasAttribute('data-feather')) {
                                    shouldReplace = true;
                                }
                                if (node.querySelector && node.querySelector('[data-feather]')) {
                                    shouldReplace = true;
                                }
                            }
                        });
                    }
                });

                if (shouldReplace && this.isLoaded) {
                    setTimeout(() => this.replaceIcons(), 100);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    // Méthode publique pour forcer la réinitialisation
    forceReplace() {
        if (this.isLoaded) {
            this.replaceIcons();
        }
    }
}

// Initialiser le gestionnaire quand le DOM est prêt
document.addEventListener('DOMContentLoaded', () => {
    window.featherIconsManager = new FeatherIconsManager();
});

// Exposer la fonction de réinitialisation globalement
window.reinitializeFeatherIcons = function() {
    if (window.featherIconsManager) {
        window.featherIconsManager.forceReplace();
    }
};