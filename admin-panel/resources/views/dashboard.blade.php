@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12 mb-3">
            <h3 class="fw-bold">Dashboard</h3>
        </div>
    </div>

    <!-- Statistics Cards - Première ligne -->
    <div class="row">
        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.1s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="package" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $productsCount }}</h4>
                            <p class="card-text text-muted mb-0">Cours</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary">+{{ $productsCount > 0 ? round(($productsCount / max($productsCount, 1)) * 100) : 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.2s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="users" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $studentsCount }}</h4>
                            <p class="card-text text-muted mb-0">Étudiants</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-success">+{{ $studentsCount > 0 ? round(($studentsCount / max($studentsCount, 1)) * 100) : 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.3s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="shopping-cart" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $ordersCount }}</h4>
                            <p class="card-text text-muted mb-0">Commandes</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-warning">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.4s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $quizResultsCount }}</h4>
                            <p class="card-text text-muted mb-0">Résultats Quiz</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-info">{{ number_format($averageQuizScore, 1) }}/100</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Deuxième ligne -->
    <div class="row">
        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.5s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="message-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $discussionsCount }}</h4>
                            <p class="card-text text-muted mb-0">Discussions</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-danger">{{ $approvedDiscussionsCount }} approuvées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.6s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="star" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $ratingsCount }}</h4>
                            <p class="card-text text-muted mb-0">Évaluations</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-secondary">{{ number_format($averageRating, 1) }}/5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.7s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-dark rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="book" class="text-white" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $booksCount }}</h4>
                            <p class="card-text text-muted mb-0">Livres</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-dark">Disponibles</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12 slide-in-left" style="animation-delay: 0.8s;">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i data-feather="folder" class="text-dark" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bolder mb-0">{{ $categoriesCount }}</h4>
                            <p class="card-text text-muted mb-0">Catégories</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-dark">Organisées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Line Chart - Évolution des commandes -->
        <div class="col-lg-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 0.9s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="trending-up" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Évolution des Commandes</h4>
                            <p class="text-white-50 mb-0">Période: {{ \Carbon\Carbon::parse($startDate ?? \Carbon\Carbon::now()->subMonths(6)->format('Y-m-d'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate ?? \Carbon\Carbon::now()->format('Y-m-d'))->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart - Évolution des quiz -->
        <div class="col-lg-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.0s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="activity" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Évolution des Quiz</h4>
                            <p class="text-white-50 mb-0">Période: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="quizChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section - Deuxième ligne -->
    <div class="row">
        <!-- Line Chart - Évolution des discussions -->
        <div class="col-lg-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.1s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="message-square" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Évolution des Discussions</h4>
                            <p class="text-white-50 mb-0">Période: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="discussionsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart - Évolution des évaluations -->
        <div class="col-lg-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.2s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="star" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Évolution des Évaluations</h4>
                            <p class="text-white-50 mb-0">Période: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ratingsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Charts Section -->
    <div class="row">
        <!-- Pie Chart - Répartition des commandes -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.3s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="pie-chart" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Commandes</h4>
                            <p class="text-white-50 mb-0">Par statut</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ordersPieChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Répartition des quiz -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.4s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="pie-chart" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Quiz</h4>
                            <p class="text-white-50 mb-0">Par résultat</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="quizPieChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Répartition des discussions -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.5s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="pie-chart" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Discussions</h4>
                            <p class="text-white-50 mb-0">Par statut</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="discussionsPieChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Répartition des évaluations -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card fade-in-up" style="animation-delay: 1.6s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="pie-chart" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Évaluations</h4>
                            <p class="text-white-50 mb-0">Par statut</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ratingsPieChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card fade-in-up" style="animation-delay: 1.7s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="zap" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Actions Rapides</h4>
                            <p class="text-white-50 mb-0">Accédez rapidement aux fonctionnalités principales</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('products.create') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Nouveau Cours</h6>
                                    <p class="card-text text-muted small">Créer un nouveau cours</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('admin.orders.index') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="shopping-cart" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Commandes</h6>
                                    <p class="card-text text-muted small">Gérer les commandes</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('admin.resultat-quizzes.index') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Résultats Quiz</h6>
                                    <p class="card-text text-muted small">Voir les résultats</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('admin.discussions.index') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="message-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Discussions</h6>
                                    <p class="card-text text-muted small">Modérer les discussions</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('admin.course-ratings.index') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="star" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Évaluations</h6>
                                    <p class="card-text text-muted small">Gérer les évaluations</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('books.create') }}" class="card quick-action-card text-decoration-none">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg bg-dark rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                        <i data-feather="book" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <h6 class="card-title text-dark mb-2">Nouveau Livre</h6>
                                    <p class="card-text text-muted small">Ajouter un livre</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration commune pour les graphiques
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    };

    // Validation des dates (uniquement si les éléments existent)
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterForm = document.getElementById('dateFilterForm');
    const filterBtn = document.getElementById('filterBtn');

    if (startDateInput && endDateInput && filterForm && filterBtn) {
        console.log('Dashboard Filter Debug:');
        console.log('Start Date:', startDateInput.value);
        console.log('End Date:', endDateInput.value);
        console.log('Form Action:', filterForm.action);

        startDateInput.addEventListener('change', function() {
            endDateInput.setAttribute('min', this.value);
            console.log('Start date changed to:', this.value);
        });

        endDateInput.addEventListener('change', function() {
            if (this.value <= startDateInput.value) {
                this.setCustomValidity('End date must be after start date');
                console.log('Date validation error: End date must be after start date');
            } else {
                this.setCustomValidity('');
                console.log('End date changed to:', this.value);
            }
        });

        filterForm.addEventListener('submit', function(e) {
            console.log('Form submitted with:');
            console.log('Start Date:', startDateInput.value);
            console.log('End Date:', endDateInput.value);
            console.log('Form Action:', this.action);

            // Optionnel: Empêcher la soumission pour debug
            // e.preventDefault();
            // console.log('Form submission prevented for debugging');
        });

        filterBtn.addEventListener('click', function(e) {
            console.log('Filter button clicked');
            console.log('Current URL:', window.location.href);
        });
    } else {
        console.log('Date filter elements not present; skipping filter listeners.');
    }

    // Auto-submit form when dates change (optional)
    // Uncomment the following lines if you want auto-submit on date change
    /*
    startDateInput.addEventListener('change', function() {
        if (endDateInput.value) {
            this.form.submit();
        }
    });

    endDateInput.addEventListener('change', function() {
        if (startDateInput.value) {
            this.form.submit();
        }
    });
    */

    // Graphique des commandes
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: @json($ordersChartLabels),
            datasets: [{
                label: 'Commandes',
                data: @json($ordersChartData),
                borderColor: 'rgb(115, 103, 240)',
                backgroundColor: 'rgba(115, 103, 240, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });

    // Graphique des quiz
    const quizCtx = document.getElementById('quizChart').getContext('2d');
    new Chart(quizCtx, {
        type: 'line',
        data: {
            labels: @json($ordersChartLabels),
            datasets: [{
                label: 'Résultats Quiz',
                data: @json($quizChartData),
                borderColor: 'rgb(40, 199, 111)',
                backgroundColor: 'rgba(40, 199, 111, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });

    // Graphique des discussions
    const discussionsCtx = document.getElementById('discussionsChart').getContext('2d');
    new Chart(discussionsCtx, {
        type: 'line',
        data: {
            labels: @json($ordersChartLabels),
            datasets: [{
                label: 'Discussions',
                data: @json($discussionsChartData),
                borderColor: 'rgb(234, 84, 85)',
                backgroundColor: 'rgba(234, 84, 85, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });

    // Graphique des évaluations
    const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
    new Chart(ratingsCtx, {
        type: 'line',
        data: {
            labels: @json($ordersChartLabels),
            datasets: [{
                label: 'Évaluations',
                data: @json($ratingsChartData),
                borderColor: 'rgb(255, 159, 67)',
                backgroundColor: 'rgba(255, 159, 67, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: commonOptions
    });

    // Configuration commune pour les graphiques en secteurs
    const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    };

    // Graphique en secteurs des commandes
    const ordersPieCtx = document.getElementById('ordersPieChart').getContext('2d');
    new Chart(ordersPieCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($ordersByStatus)),
            datasets: [{
                data: @json(array_values($ordersByStatus)),
                backgroundColor: [
                    'rgba(40, 199, 111, 0.8)',
                    'rgba(255, 159, 67, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 199, 111, 1)',
                    'rgba(255, 159, 67, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // Graphique en secteurs des quiz
    const quizPieCtx = document.getElementById('quizPieChart').getContext('2d');
    new Chart(quizPieCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($quizByStatus)),
            datasets: [{
                data: @json(array_values($quizByStatus)),
                backgroundColor: [
                    'rgba(40, 199, 111, 0.8)',
                    'rgba(234, 84, 85, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 199, 111, 1)',
                    'rgba(234, 84, 85, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // Graphique en secteurs des discussions
    const discussionsPieCtx = document.getElementById('discussionsPieChart').getContext('2d');
    new Chart(discussionsPieCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($discussionsByStatus)),
            datasets: [{
                data: @json(array_values($discussionsByStatus)),
                backgroundColor: [
                    'rgba(40, 199, 111, 0.8)',
                    'rgba(255, 159, 67, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 199, 111, 1)',
                    'rgba(255, 159, 67, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // Graphique en secteurs des évaluations
    const ratingsPieCtx = document.getElementById('ratingsPieChart').getContext('2d');
    new Chart(ratingsPieCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($ratingsByStatus)),
            datasets: [{
                data: @json(array_values($ratingsByStatus)),
                backgroundColor: [
                    'rgba(40, 199, 111, 0.8)',
                    'rgba(255, 159, 67, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 199, 111, 1)',
                    'rgba(255, 159, 67, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: pieOptions
    });
});
</script>

<style>
.card-statistics {
    transition: all 0.3s ease;
    border: none;
    border-radius: 0.428rem;
    box-shadow: 0 2px 8px 0 rgba(34, 41, 47, 0.06);
}

.card-statistics:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.24);
}

.quick-action-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 0.428rem;
    box-shadow: 0 2px 8px 0 rgba(34, 41, 47, 0.06);
}

.quick-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.24);
}

.slide-in-left {
    animation: slideInLeft 0.6s ease-out;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
