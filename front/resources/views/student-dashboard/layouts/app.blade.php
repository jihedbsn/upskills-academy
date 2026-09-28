<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard') - Upskills Academy</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/general/logo-upskills.svg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/general/logo-upskills.svg') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/student-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/udemy-course-cards.css') }}">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/student-dashboard-rtl.css') }}">
    @endif

    <style>
        /* Notification badge styles */
        .sidebar-notification-badge {
            display: none;
            position: absolute;
            top: 8px;
            right: 20px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background-color: #dc3545;
            color: white;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            line-height: 20px;
            text-align: center;
        }

        .sidebar-notification-badge[data-count]:not([data-count="0"]) {
            display: inline-block;
        }

        .nav-item {
            position: relative;
        }
    </style>

    @stack('styles')
</head>
<body class="student-dashboard-body">
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="student-dashboard-wrapper">
        <!-- Sidebar -->
        @include('student-dashboard.layouts.sidebar')

        <!-- Main Content -->
        <div class="student-dashboard-main">
            <!-- Header -->
            @include('student-dashboard.layouts.header')

            <!-- Content -->
            <main class="student-dashboard-content">
                <div class="container-fluid">
                    @include('student-dashboard.layouts.alerts')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/student-dashboard.js') }}"></script>

    <script>
        // Update notification badges
        function updateNotificationBadges() {
            fetch('/api/notifications/counts-by-type')
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.sidebar-notification-badge').forEach(badge => {
                        const notificationType = badge.getAttribute('data-notification-type');
                        const count = data[notificationType] || 0;

                        if (count > 0) {
                            badge.textContent = count;
                            badge.setAttribute('data-count', count);
                            badge.style.display = 'inline-block';
                        } else {
                            badge.textContent = '';
                            badge.setAttribute('data-count', '0');
                            badge.style.display = 'none';
                        }
                    });
                })
                .catch(error => console.error('Error fetching notification counts:', error));
        }

        // Update badges on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationBadges();

            // Update every 30 seconds
            setInterval(updateNotificationBadges, 30000);
        });
    </script>

    @stack('scripts')

    @if(session('referral_discount_popup'))
        @php $popup = session('referral_discount_popup'); @endphp
        <div class="modal fade" id="referralDiscountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="text-center text-white p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-gift-fill" style="font-size: 4rem;"></i>
                        <h3 class="fw-bold mt-3 mb-0">{{ __('messages.referral_popup_title', ['default' => 'You have a referral gift!']) }}</h3>
                    </div>
                    <div class="modal-body text-center p-4">
                        <p class="fs-5 mb-3">
                            {!! __('messages.referral_popup_body', [
                                'default' => 'Welcome! Thanks to your friend\'s invitation, you\'ll get a <strong>:percent% discount</strong> on your first order.',
                                'percent' => rtrim(rtrim(number_format($popup['percent'], 2), '0'), '.')
                            ]) !!}
                        </p>
                        <p class="text-muted small mb-4">
                            {{ __('messages.referral_popup_note', ['default' => 'The discount is automatically applied at checkout. Coupons can\'t be combined with this offer.']) }}
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('courses.index', ['locale' => app()->getLocale()]) }}" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="bi bi-cart-plus me-2"></i>{{ __('messages.referral_popup_cta', ['default' => 'Browse courses']) }}
                            </a>
                            <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">
                                {{ __('messages.maybe_later', ['default' => 'Maybe later']) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('referralDiscountModal');
                if (el && window.bootstrap) {
                    new bootstrap.Modal(el).show();
                }
            });
        </script>
    @endif
</body>
</html>
