<header class="student-dashboard-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid student-dashboard-navbar-inner">
            <!-- Mobile Toggle -->
            <button class="navbar-toggler d-lg-none student-sidebar-toggle" type="button" id="studentSidebarToggle" aria-controls="studentSidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Search Bar -->
            <div class="d-none d-md-block flex-grow-1 mx-4">
                <div class="position-relative">
                    <input type="search" class="form-control search-input" placeholder="{{ __('student_dashboard.search_placeholder') }}">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
            </div>

            <!-- Right Side -->
            <div class="navbar-nav student-dashboard-navbar-actions ms-auto align-items-center flex-row">
                <!-- Language Switcher -->
                <div class="nav-item dropdown me-2 me-md-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-globe me-1"></i>
                        <span>{{ app()->getLocale() == 'en' ? 'EN' : 'AR' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end language-dropdown">
                        @php
                            $currentRouteName = Route::currentRouteName();
                            $currentParams = Route::current() ? Route::current()->parameters() : [];
                        @endphp
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ $currentRouteName ? route($currentRouteName, array_merge($currentParams, ['locale' => 'en'])) : url('en') }}">
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ $currentRouteName ? route($currentRouteName, array_merge($currentParams, ['locale' => 'ar'])) : url('ar') }}">
                                العربية
                            </a>
                        </li>
                    </ul>
                </div>

                <style>
                .language-dropdown .dropdown-item {
                    background-color: transparent !important;
                    color: #333 !important;
                }
                .language-dropdown .dropdown-item:hover {
                    background-color: #0071ae !important;
                    color: white !important;
                }
                .language-dropdown .dropdown-item.active {
                    background-color: transparent !important;
                    color: #333 !important;
                    font-weight: 600;
                }
                .language-dropdown .dropdown-item.active:hover {
                    background-color: #0071ae !important;
                    color: white !important;
                }
                </style>

                <!-- Notification Bell -->
                <div class="nav-item dropdown me-2 me-md-3">
                    <a class="nav-link position-relative d-inline-block" href="#" role="button" data-bs-toggle="dropdown" style="padding: 0.5rem;">
                        <i class="bi bi-bell fs-5"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="position-absolute badge rounded-pill bg-danger" style="top: 2px; right: 0; font-size: 0.6rem; padding: 0.25em 0.5em; line-height: 1;">
                                {{ $unreadNotificationsCount }}
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <span class="badge bg-danger">{{ $unreadNotificationsCount }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            <!-- Messages Section with Red Dot -->
                            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <li>
                                    <a class="dropdown-item py-2 bg-light" href="{{ route('student.messages.inbox', ['locale' => app()->getLocale()]) }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-envelope-fill text-primary me-2" style="font-size: 1.2rem;"></i>
                                                <span class="fw-bold text-primary">Messages</span>
                                            </div>
                                            <span class="badge bg-danger rounded-circle d-flex align-items-center justify-content-center"
                                                  style="width: 22px; height: 22px; font-size: 0.7rem;">
                                                {{ $unreadMessagesCount }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif

                            @foreach($unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item py-2" href="{{ $notification->action_url ?? '#' }}"
                                       @if(isset($notification->type) && $notification->type === 'message')
                                       {{-- Message notifications don't need to be marked as read via notification API --}}
                                       @else
                                       onclick="markNotificationAsRead({{ $notification->id }})"
                                       @endif>
                                        <div class="d-flex align-items-start">
                                            <span class="me-2 mt-1" style="font-size: 1.2rem;">{{ $notification->icon ?? '🔔' }}</span>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold small">{{ $notification->title }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($notification->message, 60) }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">
                                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center text-primary small" href="#" onclick="markAllNotificationsAsRead()">
                                    <i class="bi bi-check2-all me-1"></i>Mark all as read
                                </a>
                            </li>
                        @else
                            <li class="dropdown-header text-center py-3">
                                <i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>
                                <span class="text-muted">No new notifications</span>
                            </li>
                        @endif
                    </ul>
                </div>

                <script>
                function markNotificationAsRead(notificationId) {
                    fetch(`/api/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).catch(err => console.error('Failed to mark notification as read:', err));
                }

                function markAllNotificationsAsRead() {
                    event.preventDefault();
                    fetch('/api/notifications/mark-all-as-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        }
                    }).catch(err => console.error('Failed to mark all as read:', err));
                }
                </script>

                <!-- User Menu -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if(Auth::user()->has_avatar)
                            <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->full_name }}" class="rounded-circle me-2" width="32" height="32">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-weight:600;">
                                {{ strtoupper(substr(Auth::user()->full_name,0,1)) }}
                            </div>
                        @endif
                        <span class="d-none d-md-block">{{ Auth::user()->full_name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('student-dashboard.profile') }}">
                            <i class="bi bi-person me-2"></i>{{ __('student_dashboard.profile') }}
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('student_dashboard.logout') }}
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
