<nav id="sidebarMenu" class="dashboard-sidebar collapse d-lg-block">
    <div class="sidebar-sticky">
        <!-- Logo -->
        <div class="sidebar-header p-3 border-bottom">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Upskills Academy" height="35" class="me-2">
                <span class="brand-text">{{ __('dashboard.academy_name') }}</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.profile.*') ? 'active' : '' }}" href="{{ route('dashboard.profile') }}">
                    <i class="bi bi-person me-3"></i>
                    {{ __('dashboard.profile') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.courses.*') ? 'active' : '' }}" href="{{ route('dashboard.courses.index') }}">
                    <i class="bi bi-play-circle me-3"></i>
                    {{ __('dashboard.my_courses') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.books.*') ? 'active' : '' }}" href="{{ route('dashboard.books.index') }}">
                    <i class="bi bi-book me-3"></i>
                    {{ __('dashboard.my_books') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.certificates.*') ? 'active' : '' }}" href="{{ route('dashboard.certificates.index') }}">
                    <i class="bi bi-award me-3"></i>
                    {{ __('dashboard.certificates') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.support.*') ? 'active' : '' }}" href="{{ route('dashboard.support.index') }}">
                    <i class="bi bi-headset me-3"></i>
                    {{ __('dashboard.support_and_ticket') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.messages.*') ? 'active' : '' }}" href="{{ route('student.messages.inbox') }}">
                    <i class="bi bi-envelope me-3"></i>
                    Messages
                    @php
                        $unreadCount = DB::table('message_recipients')->where('student_id', Auth::id())->where('is_read', 0)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-primary ms-2">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('coupons.*') ? 'active' : '' }}" href="{{ route('coupons.index') }}">
                    <i class="bi bi-bag me-3"></i>
                    {{ __('Special Offers') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.payment.*') ? 'active' : '' }}" href="{{ route('dashboard.payment.index') }}">
                    <i class="bi bi-credit-card me-3"></i>
                    {{ __('dashboard.payment') }}
                </a>
            </li>
        </ul>

        <!-- User Info -->
        <div class="sidebar-footer mt-auto p-3 border-top">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/avatar.jpg') }}" alt="User" class="rounded-circle me-2" width="40" height="40">
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ __('dashboard.student') }}</div>
                    <small class="text-muted">{{ __('dashboard.online') }}</small>
                </div>
            </div>
        </div>
    </div>
</nav>
