<!DOCTYPE html>
<html class="loading semi-dark-layout" lang="fr" data-layout="semi-dark-layout" data-textdirection="ltr">
<!-- BEGIN: Head-->
@include('layouts.head')
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="">

<style>
/* Styles personnalisés pour la navbar top */
.header-navbar {
    background: linear-gradient(135deg, #081d36 0%, #093162 60%, #297a7b 100%);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border: none;
    z-index: 1030;
}

.header-navbar .navbar-container {
    padding: 0.5rem 1rem;
}

.header-navbar .nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    transition: all 0.3s ease;
}

.header-navbar .nav-link:hover {
    color: white !important;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
}

.header-navbar .dropdown-user-link {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.header-navbar .dropdown-user-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

.header-navbar .user-name {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.header-navbar .user-status {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.header-navbar .avatar {
    margin-left: 0.5rem;
}

.header-navbar .avatar img {
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.header-navbar .avatar:hover img {
    border-color: white;
    transform: scale(1.05);
}

.header-navbar .avatar-initials {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.header-navbar .avatar:hover .avatar-initials {
    border-color: white;
    transform: scale(1.05);
}

.header-navbar .avatar-status-online {
    background-color: #28a745;
    border: 2px solid white;
}

.header-navbar .dropdown-menu {
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border-radius: 10px;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
}

.header-navbar .dropdown-item {
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    color: #6e6b7b;
}

.header-navbar .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #093162;
    transform: translateX(5px);
}

.header-navbar .dropdown-item i {
    width: 20px;
    height: 20px;
    margin-right: 0.75rem;
}

.header-navbar .dropdown-divider {
    margin: 0.5rem 0;
    border-color: #e9ecef;
}

.header-navbar .menu-toggle {
    color: white !important;
    font-size: 1.2rem;
}

.header-navbar .menu-toggle:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
}

/* Quick Actions Buttons Styles */
.quick-actions-buttons {
    gap: 0.5rem;
}

.quick-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.quick-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
}

.quick-action-btn:hover {
    transform: translateY(-2px) scale(1.05);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.1);
}

.quick-action-btn:hover::before {
    opacity: 1;
}

.quick-action-btn:active {
    transform: translateY(-1px) scale(1.02);
    transition: all 0.1s ease;
}

.quick-action-btn i {
    font-size: 16px;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.quick-action-btn:hover i {
    transform: scale(1.1);
}

/* Tooltip effect */
.quick-action-btn[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Responsive */
@media (max-width: 768px) {
    .quick-actions-buttons {
        gap: 0.25rem;
    }

    .quick-action-btn {
        width: 36px;
        height: 36px;
    }

    .quick-action-btn i {
        font-size: 14px;
    }
}

</style>

    <!-- BEGIN: Top Navbar -->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>

                <!-- Quick Actions Buttons -->
                <div class="quick-actions-buttons d-flex align-items-center ms-3">
                    <a href="{{ route('products.index') }}" class="quick-action-btn" title="Courses">
                        <i class="ficon" data-feather="book"></i>
                    </a>
                    <a href="#" class="quick-action-btn" title="Pages">
                        <i class="ficon" data-feather="file-text"></i>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="quick-action-btn" title="Orders">
                        <i class="ficon" data-feather="shopping-cart"></i>
                    </a>
                </div>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <!-- Notification Bell (only for authenticated users) -->
                @auth
                    @include('components.notification-bell')
                @endauth

                <!-- User Menu -->
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name fw-bolder">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="user-status">{{ Auth::user()->email ?? 'admin@example.com' }}</span>
                        </div>
                        <span class="avatar">
                            <div class="avatar-initials">A</div>
                            <span class="avatar-status-online"></span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="#">
                            <i class="me-50" data-feather="settings"></i>
                            <span>Settings</span>
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="me-50" data-feather="monitor"></i>
                            <span>Background</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="me-50" data-feather="log-out"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Top Navbar -->


    <!-- BEGIN: Main Menu-->
    @include('layouts.header')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @php
                $title = $title ?? "Gestion";
                $page = $page ?? 1;
            @endphp

            <div class="content-body" style="min-height: 800px">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Footer-->
    @include('layouts.footer')
    <!-- END: Footer-->

    <!-- BEGIN: Loading-->
    <div id="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>
    <!-- END: Loading-->

</body>
<!-- END: Body-->
</html>
