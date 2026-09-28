<header class="dashboard-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <!-- Mobile Toggle -->
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Search Bar -->
            <div class="d-none d-md-block flex-grow-1 mx-4">
                <div class="position-relative">
                    <input type="search" class="form-control search-input" placeholder="{{ __('dashboard.search_placeholder') }}">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
            </div>

            <!-- Right Side -->
            <div class="navbar-nav ms-auto align-items-center">
                <!-- Language Switcher -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if(app()->getLocale() == 'ar')
                            <i class="flag-icon flag-icon-sa me-2"></i>
                            العربية
                        @else
                            <i class="flag-icon flag-icon-us me-2"></i>
                            English
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="changeLanguage('en')">
                            <i class="flag-icon flag-icon-us me-2"></i>English
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="changeLanguage('ar')">
                            <i class="flag-icon flag-icon-sa me-2"></i>العربية
                        </a></li>
                    </ul>
                </div>

                <!-- Notifications -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <h6 class="dropdown-header">{{ __('dashboard.notifications') }}</h6>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0">{{ __('dashboard.course_completed') }}</p>
                                    <small class="text-muted">2 {{ __('dashboard.hours_ago') }}</small>
                                </div>
                            </div>
                        </a>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-star text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0">{{ __('dashboard.new_course_available') }}</p>
                                    <small class="text-muted">5 {{ __('dashboard.hours_ago') }}</small>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="#">{{ __('dashboard.view_all_notifications') }}</a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/img/avatar.jpg') }}" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <span class="d-none d-md-block">{{ __('dashboard.student') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('dashboard.profile') }}">
                            <i class="bi bi-person me-2"></i>{{ __('dashboard.profile') }}
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard.settings.general') }}">
                            <i class="bi bi-gear me-2"></i>{{ __('dashboard.settings') }}
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('dashboard.logout') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
