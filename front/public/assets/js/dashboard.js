/**
 * Dashboard JavaScript - Modern Design with Bootstrap 5
 * Supports English and Arabic (RTL)
 */

(function() {
    'use strict';

    // Dashboard namespace
    window.Dashboard = {
        // Configuration
        config: {
            apiUrl: '/api',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            currentLocale: document.documentElement.lang || 'en',
            isRTL: document.documentElement.dir === 'rtl'
        },

        // Initialize dashboard
        init: function() {
            this.initSidebar();
            this.initLanguageSwitcher();
            this.initNotifications();
            this.initSearch();
            this.initModals();
            this.initTooltips();
            this.initLoadingSpinner();
            this.initDarkMode();
            this.initResponsive();
            this.initPrint();
        },

        // Sidebar functionality
        initSidebar: function() {
            const sidebarToggle = document.querySelector('.js-dashboard-home-9-sidebar-toggle');
            const sidebar = document.querySelector('#sidebarMenu');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        },

        // Language switcher
        initLanguageSwitcher: function() {
            window.changeLanguage = function(locale) {
                Dashboard.showLoading();
                
                fetch('/change-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': Dashboard.config.csrfToken
                    },
                    body: JSON.stringify({ locale: locale })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        Dashboard.showNotification('Error changing language', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Dashboard.showNotification('Error changing language', 'error');
                })
                .finally(() => {
                    Dashboard.hideLoading();
                });
            };
        },

        // Notifications
        initNotifications: function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });

            // Mark notifications as read
            const notificationItems = document.querySelectorAll('.notification-dropdown .dropdown-item');
            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.classList.add('read');
                });
            });
        },

        // Search functionality
        initSearch: function() {
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length >= 2) {
                        searchTimeout = setTimeout(() => {
                            Dashboard.performSearch(query);
                        }, 300);
                    }
                });
            }
        },

        // Perform search
        performSearch: function(query) {
            // Implement search functionality here
            console.log('Searching for:', query);
            
            // Example: Redirect to search results page
            // window.location.href = `/dashboard/search?q=${encodeURIComponent(query)}`;
        },

        // Modal functionality
        initModals: function() {
            // Initialize all modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                new bootstrap.Modal(modal);
            });

            // Quick actions modal
            const quickActionsModal = document.getElementById('quickActionsModal');
            if (quickActionsModal) {
                const modal = new bootstrap.Modal(quickActionsModal);
                
                // Handle quick action clicks
                const actionButtons = quickActionsModal.querySelectorAll('.btn');
                actionButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        modal.hide();
                    });
                });
            }
        },

        // Tooltips
        initTooltips: function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },

        // Loading spinner
        initLoadingSpinner: function() {
            // Show loading spinner
            window.showLoading = function() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.style.display = 'flex';
                }
            };

            // Hide loading spinner
            window.hideLoading = function() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.style.display = 'none';
                }
            };
        },

        // Dark mode toggle
        initDarkMode: function() {
            const darkModeToggle = document.querySelector('.js-darkmode-toggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    const html = document.documentElement;
                    const currentTheme = html.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    html.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    // Update icon
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = newTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
                    }
                });

                // Load saved theme
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    document.documentElement.setAttribute('data-bs-theme', savedTheme);
                    const icon = darkModeToggle.querySelector('i');
                    if (icon) {
                        icon.className = savedTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
                    }
                }
            }
        },

        // Responsive functionality
        initResponsive: function() {
            // Handle window resize
            window.addEventListener('resize', function() {
                const sidebar = document.querySelector('#sidebarMenu');
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                }
            });

            // Handle orientation change
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 100);
            });
        },

        // Print functionality
        initPrint: function() {
            // Print button functionality
            const printButtons = document.querySelectorAll('[onclick*="print"]');
            printButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.print();
                });
            });
        },

        // Show notification
        showNotification: function(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const iconClass = {
                'success': 'bi-check-circle',
                'error': 'bi-exclamation-triangle',
                'warning': 'bi-exclamation-triangle',
                'info': 'bi-info-circle'
            }[type] || 'bi-info-circle';

            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            const container = document.querySelector('.container-fluid');
            if (container) {
                container.insertAdjacentHTML('afterbegin', alertHtml);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    const alert = container.querySelector('.alert');
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            }
        },

        // Show loading
        showLoading: function() {
            if (window.showLoading) {
                window.showLoading();
            }
        },

        // Hide loading
        hideLoading: function() {
            if (window.hideLoading) {
                window.hideLoading();
            }
        },

        // Utility functions
        utils: {
            // Format date
            formatDate: function(date, locale = 'en') {
                return new Intl.DateTimeFormat(locale, {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }).format(new Date(date));
            },

            // Format number
            formatNumber: function(number, locale = 'en') {
                return new Intl.NumberFormat(locale).format(number);
            },

            // Format currency
            formatCurrency: function(amount, currency = 'USD', locale = 'en') {
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency
                }).format(amount);
            },

            // Debounce function
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            // Throttle function
            throttle: function(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
            }
        }
    };

    // Initialize dashboard when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Dashboard.init();
        });
    } else {
        Dashboard.init();
    }

    // Export for global access
    window.Dashboard = Dashboard;

})();