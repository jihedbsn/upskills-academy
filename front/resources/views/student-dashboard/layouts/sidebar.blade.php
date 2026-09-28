<nav id="studentSidebarMenu" class="student-dashboard-sidebar d-lg-block">
    <div class="sidebar-sticky">
        <!-- Logo -->
        <div class="sidebar-header p-3 border-bottom">
            <div class="d-flex align-items-center justify-content-center">
                <a href="{{ route('accueil.index') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
                    <img src="{{ asset('assets/img/general/logo-upskills.svg') }}" alt="Upskills Academy" height="42">
                </a>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.profile') ? 'active' : '' }}" href="{{ route('student-dashboard.profile') }}">
                    <i class="bi bi-person me-3"></i>
                    {{ __('student_dashboard.profile') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.courses.*') ? 'active' : '' }}" href="{{ route('student-dashboard.courses.index') }}">
                    <i class="bi bi-play-circle me-3"></i>
                    {{ __('student_dashboard.my_courses') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.books.*') ? 'active' : '' }}" href="{{ route('student-dashboard.books.index') }}">
                    <i class="bi bi-book me-3"></i>
                    {{ __('student_dashboard.my_books') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.certificates.*') ? 'active' : '' }}" href="{{ route('student-dashboard.certificates.index') }}">
                    <i class="bi bi-award me-3"></i>
                    {{ __('student_dashboard.my_certificates') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.old-certificates.index') ? 'active' : '' }}" href="{{ route('student-dashboard.old-certificates.index') }}">
                    <i class="bi bi-archive me-3"></i>
                    {{ __('messages.certificates') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.orders.*') ? 'active' : '' }}" href="{{ route('student-dashboard.orders.index') }}">
                    <i class="bi bi-receipt me-3"></i>
                    {{ __('messages.my_orders') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.installments.*') ? 'active' : '' }}" href="{{ route('student-dashboard.installments.index') }}">
                    <i class="bi bi-credit-card-2-front me-3"></i>
                    {{ __('messages.installments') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.course-extensions.*') ? 'active' : '' }}" href="{{ route('student-dashboard.course-extensions.index') }}">
                    <i class="bi bi-arrow-clockwise me-3"></i>
                    {{ __('student_dashboard.ext_course_extensions') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.calendar') ? 'active' : '' }}" href="{{ route('student-dashboard.calendar') }}">
                    <i class="bi bi-calendar3 me-3"></i>
                    {{ __('messages.my_calendar') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.course-schedule') ? 'active' : '' }}" href="{{ route('student-dashboard.course-schedule') }}">
                    <i class="bi bi-calendar-event me-3"></i>
                    {{ __('messages.course_schedule') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.history.*') ? 'active' : '' }}" href="{{ route('student-dashboard.history.index') }}">
                    <i class="bi bi-clock-history me-3"></i>
                    {{ __('student_dashboard.quiz_exam_history') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.practical-exam.*') ? 'active' : '' }}" href="{{ route('student.practical-exam.index', ['locale' => app()->getLocale()]) }}">
                    <i class="bi bi-clipboard-check me-3"></i>
                    {{ __('messages.practical_exams') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.zoom-meetings.*') ? 'active' : '' }}" href="{{ route('student-dashboard.zoom-meetings.index') }}">
                    <i class="bi bi-camera-video me-3"></i>
                    {{ __('messages.zoom_meetings') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.support.*') ? 'active' : '' }}" href="{{ route('student-dashboard.support.index') }}">
                    <i class="bi bi-headset me-3"></i>
                    {{ __('student_dashboard.support_and_ticket') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.messages.*') ? 'active' : '' }}" href="{{ route('student.messages.inbox') }}">
                    <i class="bi bi-envelope me-3"></i>
                    {{ __('messages.inbox') }}
                    <span class="sidebar-notification-badge" data-notification-type="admin_message"></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.special-offers.*') ? 'active' : '' }}" href="{{ route('student-dashboard.special-offers.index') }}">
                    <i class="bi bi-bag me-3"></i>
                    {{ __('coupon.special_offers') }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.referrals.*') ? 'active' : '' }}" href="{{ route('student-dashboard.referrals.index') }}">
                    <i class="bi bi-people me-3"></i>
                    {{ __('messages.referrals') }}
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student-dashboard.payment.*') ? 'active' : '' }}" href="{{ route('student-dashboard.payment.index') }}">
                    <i class="bi bi-credit-card me-3"></i>
                    {{ __('student_dashboard.payment') }}
                </a>
            </li> --}}
        </ul>

        <!-- User Info -->
        <div class="sidebar-footer mt-auto p-3 border-top">
            <div class="dropdown">
                <div class="d-flex align-items-center" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                    <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->full_name }}" class="rounded-circle me-2" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ Auth::user()->full_name }}</div>
                        <small class="text-muted">{{ __('student_dashboard.online') }}</small>
                    </div>
                    <i class="bi bi-chevron-down text-muted"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end w-100">
                    <li>
                        <a class="dropdown-item" href="{{ route('student-dashboard.profile') }}">
                            <i class="bi bi-person me-2"></i>{{ __('student_dashboard.profile') }}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('student_dashboard.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
