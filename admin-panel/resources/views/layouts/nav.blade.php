<style>
.navbar-brand-wrapper .navbar-brand {
    text-decoration: none;
    color: #093162;
    font-weight: 600;
}

.navbar-brand-wrapper .brand-text {
    color: #093162;
    font-weight: 600;
    margin-bottom: 0;
}

.navbar-brand-wrapper small {
    font-size: 0.75rem;
    color: #6e6b7b;
}

.navbar-nav.flex-grow-1 {
    justify-content: center;
}

.navbar-nav .nav-link {
    padding: 0.5rem 1rem;
    color: #6e6b7b;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #093162;
    background-color: rgba(9, 49, 98, 0.08);
    border-radius: 0.428rem;
}

.navbar-nav .dropdown-menu {
    border: none;
    box-shadow: 0 5px 25px 0 rgba(0, 0, 0, 0.1);
    border-radius: 0.428rem;
}

.navbar-nav .dropdown-item {
    padding: 0.5rem 1rem;
    color: #6e6b7b;
    transition: all 0.3s ease;
}

.navbar-nav .dropdown-item:hover {
    background-color: rgba(9, 49, 98, 0.08);
    color: #093162;
}
</style>

<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon fa fa-bars"></i></a></li>
            </ul>
            <ul class="nav navbar-nav bookmark-icons">
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon fa fa-moon-o"></i></a></li>
            </ul>
        </div>

        <!-- Logo/Brand -->
        <div class="navbar-brand-wrapper d-flex align-items-center me-3">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <h4 class="brand-text mb-0">Upskills Academy</h4>
                <small class="text-muted">Admin Panel</small>
            </a>
        </div>

        <!-- Main Navigation -->
        <ul class="nav navbar-nav align-items-center flex-grow-1 justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fa fa-home me-1"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="contentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-edit me-1"></i>
                    Content
                </a>
                <ul class="dropdown-menu" aria-labelledby="contentDropdown">
                    <li><a class="dropdown-item" href="{{ route('pages.index') }}">
                        <i class="fa fa-file-text-o me-2"></i>Pages
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('products.index') }}">
                        <i class="fa fa-book me-2"></i>Courses
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('categories.index') }}">
                        <i class="fa fa-folder me-2"></i>Categories
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('books.index') }}">
                        <i class="fa fa-book me-2"></i>Books
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('resources.index') }}">
                        <i class="fa fa-download me-2"></i>Resources
                    </a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="blogsArticlesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-file-text-o me-1"></i>
                    Blogs & Articles
                </a>
                <ul class="dropdown-menu" aria-labelledby="blogsArticlesDropdown">
                    <li><a class="dropdown-item" href="{{ route('blogs.index') }}">
                        <i class="fa fa-edit me-2"></i>Blog
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index') }}">
                        <i class="fa fa-file-text-o me-2"></i>Article
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('carousels.index') }}">
                        <i class="fa fa-images me-2"></i>Carousels
                    </a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="educationalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-graduation-cap me-1"></i>
                    Education
                </a>
                <ul class="dropdown-menu" aria-labelledby="educationalDropdown">
                    <li><a class="dropdown-item" href="{{ route('teachers.index') }}">
                        <i class="fa fa-users me-2"></i>Teachers
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('quizzes.index') }}">
                        <i class="fa fa-question-circle me-2"></i>Quizzes
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('training-cases.index') }}">
                        <i class="fa fa-file-text me-2"></i>Training Cases
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('reponse-questions.index') }}">
                        <i class="fa fa-check-circle me-2"></i>Responses
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.course-sessions.index') }}">
                        <i class="fa fa-calendar me-2"></i>Course Sessions
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('zoom-meetings.index') }}">
                        <i class="fa fa-video-camera me-2"></i>Zoom Meetings
                    </a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="studentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-user-check me-1"></i>
                    Students
                </a>
                <ul class="dropdown-menu" aria-labelledby="studentsDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                        <i class="fa fa-shopping-cart me-2"></i>Orders
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.resultat-quizzes.index') }}">
                        <i class="fa fa-trophy me-2"></i>Quiz Results
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.discussions.index') }}">
                        <i class="fa fa-comments me-2"></i>Discussions
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.course-ratings.index') }}">
                        <i class="fa fa-star me-2"></i>Course Ratings
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.referrals.index') }}">
                        <i class="fa fa-share-alt me-2"></i>Referrals
                        @php
                            $pendingReferrals = \App\Models\Referral::where('status', 'pending')->count();
                        @endphp
                        @if($pendingReferrals > 0)
                            <span class="badge bg-danger ms-2">{{ $pendingReferrals }}</span>
                        @endif
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.chat.index') }}">
                        <i class="fa fa-commenting me-2"></i>Live Chat
                        @php
                            $unreadChats = \App\Models\ChatConversation::where('unread_admin_count', '>', 0)->count();
                        @endphp
                        @if($unreadChats > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadChats }}</span>
                        @endif
                    </a></li>
                </ul>
            </li>

        </ul>

        <ul class="nav navbar-nav align-items-center ms-auto">
            <!-- Notification Bell -->
            @include('components.notification-bell')

            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">anis</span>
                        <span class="user-status">Admin</span>
                    </div>
                    <span class="avatar">
                        <img class="round" src="/img/anis.jpg" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <div class="dropdown-divider"></div>
                    <!-- Formulaire de déconnexion -->
                </div>
            </li>
        </ul>
    </div>
</nav>
