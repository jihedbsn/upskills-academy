// Dashboard Mobile Menu Functionality - Version Moderne
document.addEventListener('DOMContentLoaded', function() {

    // ===== MOBILE MENU TOGGLE =====
    const mobileMenuToggle = document.querySelector('[data-el-toggle=".js-dashboard-mobile-menu-toggle"]');
    const mobileMenu = document.querySelector('.js-dashboard-mobile-menu-toggle');
    const mobileMenuClose = document.querySelector('.dashboard-mobile-menu-close');
    const mobileMenuBg = document.querySelector('.dashboard-mobile-menu-bg');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileMenu.classList.add('-is-active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', function(e) {
            e.preventDefault();
            mobileMenu.classList.remove('-is-active');
            document.body.style.overflow = '';
        });
    }

    if (mobileMenuBg) {
        mobileMenuBg.addEventListener('click', function(e) {
            e.preventDefault();
            mobileMenu.classList.remove('-is-active');
            document.body.style.overflow = '';
        });
    }

    // Close mobile menu when clicking on menu items
    const mobileMenuItems = document.querySelectorAll('.js-dashboard-mobile-menu-toggle .menu__nav a');
    mobileMenuItems.forEach(function(item) {
        item.addEventListener('click', function() {
            mobileMenu.classList.remove('-is-active');
            document.body.style.overflow = '';
        });
    });

    // ===== SIDEBAR TOGGLE FOR DESKTOP =====
    const sidebarToggle = document.querySelector('.js-dashboard-home-9-sidebar-toggle');
    const dashboard = document.querySelector('.js-dashboard-home-9');

    if (sidebarToggle && dashboard) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            dashboard.classList.toggle('-sidebar-open');
        });
    }

    // ===== PROFILE MENU TOGGLE =====
    const profileToggle = document.querySelector('[data-el-toggle=".js-profile-toggle"]');
    const profileMenu = document.querySelector('.js-profile-toggle');

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            profileMenu.classList.toggle('-is-active');
        });

        // Close profile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('-is-active');
            }
        });
    }

    // ===== SEARCH TOGGLE =====
    const searchToggle = document.querySelector('[data-el-toggle=".js-search-toggle"]');
    const searchMenu = document.querySelector('.js-search-toggle');

    if (searchToggle && searchMenu) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchMenu.classList.toggle('-is-active');
        });

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchToggle.contains(e.target) && !searchMenu.contains(e.target)) {
                searchMenu.classList.remove('-is-active');
            }
        });
    }

    // ===== NOTIFICATION TOGGLE =====
    const notifToggle = document.querySelector('[data-el-toggle=".js-notif-toggle"]');
    const notifMenu = document.querySelector('.js-notif-toggle');

    if (notifToggle && notifMenu) {
        notifToggle.addEventListener('click', function(e) {
            e.preventDefault();
            notifMenu.classList.toggle('-is-active');
        });

        // Close notification when clicking outside
        document.addEventListener('click', function(e) {
            if (!notifToggle.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.remove('-is-active');
            }
        });
    }

    // ===== RTL SUPPORT FOR MOBILE MENU =====
    const isRTL = document.documentElement.dir === 'rtl';
    if (isRTL) {
        const mobileMenuContent = document.querySelector('.dashboard-mobile-menu__content');
        if (mobileMenuContent) {
            mobileMenuContent.style.transform = 'translateX(100%)';
        }
    }

    // ===== KEYBOARD SUPPORT =====
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (mobileMenu && mobileMenu.classList.contains('-is-active')) {
                mobileMenu.classList.remove('-is-active');
                document.body.style.overflow = '';
            }
            if (profileMenu && profileMenu.classList.contains('-is-active')) {
                profileMenu.classList.remove('-is-active');
            }
            if (searchMenu && searchMenu.classList.contains('-is-active')) {
                searchMenu.classList.remove('-is-active');
            }
            if (notifMenu && notifMenu.classList.contains('-is-active')) {
                notifMenu.classList.remove('-is-active');
            }
        }
    });

    // ===== SMOOTH SCROLLING =====
    const mobileMenuLinks = document.querySelectorAll('.js-dashboard-mobile-menu-toggle a[href^="#"]');
    mobileMenuLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== LOADING STATES =====
    const dashboardLinks = document.querySelectorAll('.dashboard__sidebar a, .js-dashboard-mobile-menu-toggle a');
    dashboardLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            showLoadingIndicator();
        });
    });

    // ===== ANIMATIONS ET EFFETS VISUELS =====

    // Animation des cartes de statistiques
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(function() {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation des cartes de cours
    const courseCards = document.querySelectorAll('.course-card');
    courseCards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.9)';

        setTimeout(function() {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, index * 150);
    });

    // Animation des barres de progression
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(function(bar) {
        const width = bar.style.width;
        bar.style.width = '0%';

        setTimeout(function() {
            bar.style.transition = 'width 1s ease-out';
            bar.style.width = width;
        }, 500);
    });

    // Animation des cercles de progression
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(function(circle) {
        const progress = circle.getAttribute('data-progress');
        const angle = (progress / 100) * 360;

        circle.style.background = `conic-gradient(var(--primary-color) 0deg ${angle}deg, transparent ${angle}deg 360deg)`;
    });

    // ===== INTERACTIONS AVANCÉES =====

    // Hover effects pour les cartes
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    dashboardCards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Effet de ripple pour les boutons
    const buttons = document.querySelectorAll('.btn-modern');
    buttons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    });

    // ===== NOTIFICATIONS DYNAMIQUES =====
    const notificationBadge = document.querySelector('.notification-badge');
    if (notificationBadge) {
        // Simuler des notifications en temps réel
        setInterval(function() {
            const currentCount = parseInt(notificationBadge.textContent);
            if (Math.random() > 0.8) { // 20% de chance
                notificationBadge.textContent = currentCount + 1;
                notificationBadge.style.animation = 'pulse 0.5s ease-in-out';
                setTimeout(function() {
                    notificationBadge.style.animation = '';
                }, 500);
            }
        }, 10000); // Vérifier toutes les 10 secondes
    }

    // ===== FONCTIONS UTILITAIRES =====

    function showLoadingIndicator() {
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'loading-indicator';
        loadingIndicator.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loadingIndicator);

        setTimeout(function() {
            if (loadingIndicator.parentNode) {
                loadingIndicator.parentNode.removeChild(loadingIndicator);
            }
        }, 1000);
    }

    // ===== RESPONSIVE BEHAVIOR =====

    function handleResize() {
        const width = window.innerWidth;

        if (width <= 1024) {
            // Masquer la sidebar sur mobile
            const sidebar = document.querySelector('.dashboard__sidebar');
            if (sidebar) {
                sidebar.style.transform = 'translateX(-100%)';
            }
        } else {
            // Afficher la sidebar sur desktop
            const sidebar = document.querySelector('.dashboard__sidebar');
            if (sidebar) {
                sidebar.style.transform = 'translateX(0)';
            }
        }
    }

    // Écouter les changements de taille d'écran
    window.addEventListener('resize', handleResize);
    handleResize(); // Appel initial

    // ===== THEME SUPPORT =====

    // Détecter le thème système
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

    function updateTheme(e) {
        if (e.matches) {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    }

    prefersDark.addListener(updateTheme);
    updateTheme(prefersDark);

    // ===== PERFORMANCE OPTIMIZATIONS =====

    // Lazy loading pour les images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // ===== ACCESSIBILITY IMPROVEMENTS =====

    // Focus management
    const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            // Gérer la navigation au clavier
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    });

    // ===== ANALYTICS ET TRACKING =====

    // Tracker les interactions utilisateur
    const trackableElements = document.querySelectorAll('[data-track]');
    trackableElements.forEach(function(element) {
        element.addEventListener('click', function() {
            const action = this.getAttribute('data-track');
            console.log('User action tracked:', action);
            // Ici vous pouvez envoyer les données à votre système d'analytics
        });
    });

    console.log('Dashboard moderne initialisé avec succès! 🚀');
});

// ===== STYLES CSS DYNAMIQUES =====

// Ajouter les styles pour les effets visuels
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn-modern {
        position: relative;
        overflow: hidden;
    }
    
    .dark-theme {
        --primary-color: #8b5cf6;
        --secondary-color: #1f2937;
        --text-dark: #f9fafb;
        --text-light: #d1d5db;
        --border-color: #374151;
    }
    
    .lazy {
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .lazy.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(style);