/**
 * Student Dashboard JavaScript
 * Modern interactive dashboard functionality
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeDashboard();
    });

    function initializeDashboard() {
        // Initialize all components
        initializeSidebar();
        initializeLanguageSwitcher();
        initializeNotifications();
        initializeSearch();
        initializeModals();
        initializeTooltips();
        initializeLoadingSpinner();
        initializeResponsive();
        initializePrint();

        // Initialize utility functions
        initializeUtilities();

        console.log('Student Dashboard initialized successfully');
    }

    // Sidebar functionality — mobile drawer with backdrop + scroll lock
    function initializeSidebar() {
        const sidebarToggle = document.getElementById('studentSidebarToggle');
        const sidebar = document.getElementById('studentSidebarMenu');

        if (sidebar) {
            // Backdrop element (created once)
            let backdrop = document.querySelector('.student-sidebar-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'student-sidebar-backdrop';
                document.body.appendChild(backdrop);
            }

            const openSidebar = function () {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.classList.add('sidebar-open');
                if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'true');
            };
            const closeSidebar = function () {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'false');
            };

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
                });
            }

            // Close on backdrop tap
            backdrop.addEventListener('click', closeSidebar);

            // Close when a nav link is tapped (mobile)
            sidebar.querySelectorAll('.nav-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            // Reset on resize back to desktop
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) closeSidebar();
            });
        }

        // Active link highlighting
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.student-dashboard-sidebar .nav-link');

        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }

    // Language switcher functionality
    function initializeLanguageSwitcher() {
        // Language change function (global scope for onclick)
        window.changeLanguage = function(locale) {
            showLoadingSpinner();

            fetch('/change-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ locale: locale })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to apply language change
                        window.location.reload();
                    } else {
                        hideLoadingSpinner();
                        showNotification('Error changing language', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingSpinner();
                    console.error('Language change error:', error);
                    showNotification('Error changing language', 'error');
                });
        };

        // Fix language dropdown visibility on mobile
        const languageDropdownToggle = document.querySelector('.language-menu-item .dropdown-toggle');
        const languageDropdownMenu = document.querySelector('.language-menu-item .dropdown-menu');

        if (languageDropdownToggle && languageDropdownMenu) {
            // Function to update dropdown position
            function updateDropdownPosition() {
                if (window.innerWidth <= 991) {
                    const toggleRect = languageDropdownToggle.getBoundingClientRect();

                    if (toggleRect) {
                        // Calculate position relative to viewport
                        let topPosition = toggleRect.bottom + 5;
                        let leftPosition = toggleRect.left;

                        // Ensure dropdown doesn't go off screen
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        const dropdownWidth = Math.max(toggleRect.width, 150);
                        const dropdownHeight = 100; // Estimated height

                        // Adjust if dropdown would go off right edge
                        if (leftPosition + dropdownWidth > viewportWidth - 10) {
                            leftPosition = viewportWidth - dropdownWidth - 10;
                        }

                        // Adjust if dropdown would go off bottom edge
                        if (topPosition + dropdownHeight > viewportHeight - 10) {
                            topPosition = toggleRect.top - dropdownHeight - 5;
                        }

                        // Ensure dropdown doesn't go off left edge
                        if (leftPosition < 10) {
                            leftPosition = 10;
                        }

                        // Apply styles
                        languageDropdownMenu.style.position = 'fixed';
                        languageDropdownMenu.style.top = topPosition + 'px';
                        languageDropdownMenu.style.left = leftPosition + 'px';
                        languageDropdownMenu.style.width = dropdownWidth + 'px';
                        languageDropdownMenu.style.zIndex = '10002';
                        languageDropdownMenu.style.display = 'block';
                        languageDropdownMenu.style.visibility = 'visible';
                        languageDropdownMenu.style.opacity = '1';
                        languageDropdownMenu.style.maxWidth = (viewportWidth - 20) + 'px';
                    }
                }
            }

            // Listen for Bootstrap dropdown show event
            languageDropdownToggle.addEventListener('show.bs.dropdown', function(e) {
                // Don't interfere with Bootstrap's default behavior on desktop
                // Only adjust position on mobile
            });

            // Listen for shown event (after dropdown is shown)
            languageDropdownToggle.addEventListener('shown.bs.dropdown', function() {
                // Force visibility on mobile and adjust position
                if (window.innerWidth <= 991) {
                    // Ensure dropdown is visible first
                    languageDropdownMenu.style.display = 'block';
                    languageDropdownMenu.style.visibility = 'visible';
                    languageDropdownMenu.style.opacity = '1';

                    // Then update position
                    setTimeout(() => {
                        updateDropdownPosition();
                    }, 10);

                    // Update position again after Bootstrap has positioned it
                    setTimeout(() => {
                        updateDropdownPosition();
                    }, 50);

                    // Update on scroll/resize
                    const positionUpdater = setInterval(() => {
                        if (languageDropdownMenu.classList.contains('show')) {
                            updateDropdownPosition();
                        } else {
                            clearInterval(positionUpdater);
                        }
                    }, 100);
                }
            });

            // Reset position on hide
            languageDropdownToggle.addEventListener('hide.bs.dropdown', function() {
                if (window.innerWidth <= 991) {
                    languageDropdownMenu.style.position = '';
                    languageDropdownMenu.style.top = '';
                    languageDropdownMenu.style.left = '';
                    languageDropdownMenu.style.width = '';
                }
            });

            // Update position on window resize
            window.addEventListener('resize', function() {
                if (languageDropdownMenu.classList.contains('show') && window.innerWidth <= 991) {
                    updateDropdownPosition();
                }
            });
        }
    }

    // Notifications functionality
    function initializeNotifications() {
        const notificationDropdowns = document.querySelectorAll('.notification-dropdown');

        notificationDropdowns.forEach(dropdown => {
            const items = dropdown.querySelectorAll('.dropdown-item');

            items.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!this.classList.contains('dropdown-header')) {
                        // Mark as read functionality
                        const badge = document.querySelector('.badge');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent);
                            if (currentCount > 0) {
                                badge.textContent = currentCount - 1;
                                if (currentCount - 1 === 0) {
                                    badge.style.display = 'none';
                                }
                            }
                        }
                    }
                });
            });
        });
    }

    // Search functionality
    function initializeSearch() {
        const searchInput = document.querySelector('.search-input');

        if (searchInput) {
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        performSearch(query);
                    }, 300);
                }
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query) {
                        performSearch(query);
                    }
                }
            });
        }
    }

    function performSearch(query) {
        // Implement search functionality
        console.log('Searching for:', query);
        showNotification(`Searching for: ${query}`, 'info');

        // Example search implementation
        // fetch(`/student-dashboard/search?q=${encodeURIComponent(query)}`)
        //     .then(response => response.json())
        //     .then(data => {
        //         displaySearchResults(data);
        //     });
    }

    // Modal functionality
    function initializeModals() {
        const modals = document.querySelectorAll('.modal');

        modals.forEach(modal => {
            // Auto-hide modals after 5 seconds (for demo purposes)
            const autoHide = modal.dataset.autoHide;
            if (autoHide === 'true') {
                setTimeout(() => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }, 5000);
            }

            // Handle modal events
            modal.addEventListener('shown.bs.modal', function() {
                console.log('Modal shown:', this.id);
            });

            modal.addEventListener('hidden.bs.modal', function() {
                console.log('Modal hidden:', this.id);
            });
        });
    }

    // Tooltip initialization
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Loading spinner functionality
    function initializeLoadingSpinner() {
        window.showLoadingSpinner = function() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.display = 'flex';
            }
        };

        window.hideLoadingSpinner = function() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.display = 'none';
            }
        };

        // Auto-hide spinner after 3 seconds (fallback)
        setTimeout(() => {
            hideLoadingSpinner();
        }, 3000);
    }

    // Responsive functionality
    function initializeResponsive() {
        // Handle responsive breakpoints
        function handleResize() {
            const isMobile = window.innerWidth < 768;
            const isTablet = window.innerWidth >= 768 && window.innerWidth < 992;
            const isDesktop = window.innerWidth >= 992;

            // Add responsive classes
            document.body.classList.toggle('mobile-view', isMobile);
            document.body.classList.toggle('tablet-view', isTablet);
            document.body.classList.toggle('desktop-view', isDesktop);

            // Adjust sidebar behavior
            const sidebar = document.getElementById('studentSidebarMenu');
            if (sidebar) {
                if (isMobile || isTablet) {
                    sidebar.classList.remove('d-lg-block');
                } else {
                    sidebar.classList.add('d-lg-block');
                }
            }
        }

        // Initial call
        handleResize();

        // Listen for resize events
        window.addEventListener('resize', debounce(handleResize, 250));
    }

    // Print functionality
    function initializePrint() {
        // Handle print button clicks
        const printButtons = document.querySelectorAll('[onclick*="print"]');

        printButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                window.print();
            });
        });

        // Custom print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .student-dashboard-sidebar,
                .student-dashboard-header,
                .btn,
                .modal,
                .no-print {
                    display: none !important;
                }

                .student-dashboard-main {
                    margin-left: 0 !important;
                }

                .card {
                    border: 1px solid #ccc !important;
                    box-shadow: none !important;
                }

                .table {
                    font-size: 12px !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Utility functions
    function initializeUtilities() {
        // Notification system
        window.showNotification = function(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const iconClass = getNotificationIcon(type);

            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        };

        // Format functions
        window.formatDate = function(date) {
            return new Date(date).toLocaleDateString();
        };

        window.formatCurrency = function(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        };

        window.formatNumber = function(number) {
            return new Intl.NumberFormat().format(number);
        };

        // Debounce function
        window.debounce = function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Throttle function
        window.throttle = function(func, limit) {
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
        };
    }

    // Helper functions
    function getNotificationIcon(type) {
        const icons = {
            success: 'bi bi-check-circle',
            error: 'bi bi-exclamation-triangle',
            warning: 'bi bi-exclamation-triangle',
            info: 'bi bi-info-circle'
        };
        return icons[type] || icons.info;
    }

    // Chart functionality (if needed)
    function initializeCharts() {
        // Example chart initialization
        const chartElements = document.querySelectorAll('[data-chart]');

        chartElements.forEach(element => {
            const chartType = element.dataset.chart;
            const chartData = JSON.parse(element.dataset.chartData || '{}');

            // Initialize chart based on type
            switch (chartType) {
                case 'progress':
                    initializeProgressChart(element, chartData);
                    break;
                case 'line':
                    initializeLineChart(element, chartData);
                    break;
                case 'bar':
                    initializeBarChart(element, chartData);
                    break;
            }
        });
    }

    function initializeProgressChart(element, data) {
        // Simple progress bar animation
        const progressBar = element.querySelector('.progress-bar');
        if (progressBar && data.percentage) {
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.width = data.percentage + '%';
            }, 100);
        }
    }

    function initializeLineChart(element, data) {
        // Placeholder for line chart
        console.log('Line chart initialized for:', element.id);
    }

    function initializeBarChart(element, data) {
        // Placeholder for bar chart
        console.log('Bar chart initialized for:', element.id);
    }

    // Initialize additional features
    initializeCharts();

})();
