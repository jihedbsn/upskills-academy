@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa fa-chart-bar me-2"></i>
            Quiz Statistics
        </h1>
        <div class="page-actions">
            <a href="{{ route('historique-quiz.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to History
            </a>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-list fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total_attempts'] }}</h4>
                    <small>Total Attempts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['successful_attempts'] }}</h4>
                    <small>Successful</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fa fa-times-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['failed_attempts'] }}</h4>
                    <small>Failed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fa fa-chart-line fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ number_format($stats['average_score'], 1) }}%</h4>
                    <small>Average Score</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques additionnelles -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_students'] }}</h4>
                    <small>Unique Students</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-question-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_quizzes'] }}</h4>
                    <small>Unique Quizzes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <i class="fa fa-book fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_courses'] }}</h4>
                    <small>Unique Courses</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des statistiques quotidiennes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-line me-2"></i>
                        Daily Statistics (Last 30 Days)
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="dailyStatsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top étudiants -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-trophy me-2"></i>
                        Top Students (by Average Score)
                    </h4>
                </div>
                <div class="card-body">
                    @if($topStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Avg Score</th>
                                        <th>Attempts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStudents as $index => $student)
                                        <tr>
                                            <td>
                                                @if($index == 0)
                                                    <i class="fa fa-medal text-warning"></i>
                                                @elseif($index == 1)
                                                    <i class="fa fa-medal text-secondary"></i>
                                                @elseif($index == 2)
                                                    <i class="fa fa-medal text-warning" style="color: #CD7F32 !important;"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $student->student->full_name ?? 'Unknown' }}</div>
                                                <small class="text-muted">{{ $student->student->email ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ number_format($student->avg_score, 1) }}%</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $student->attempts }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-trophy fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No students with sufficient attempts found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top quiz -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-question-circle me-2"></i>
                        Most Popular Quizzes
                    </h4>
                </div>
                <div class="card-body">
                    @if($topQuizzes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Quiz</th>
                                        <th>Attempts</th>
                                        <th>Avg Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topQuizzes as $index => $quiz)
                                        <tr>
                                            <td>
                                                @if($index == 0)
                                                    <i class="fa fa-fire text-danger"></i>
                                                @elseif($index == 1)
                                                    <i class="fa fa-fire text-warning"></i>
                                                @elseif($index == 2)
                                                    <i class="fa fa-fire text-info"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $quiz->quiz->name_en ?? 'Unknown Quiz' }}</div>
                                                @if($quiz->quiz->name_ar)
                                                    <small class="text-muted">{{ $quiz->quiz->name_ar }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $quiz->attempts }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ number_format($quiz->avg_score, 1) }}%</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-question-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No quiz attempts found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Taux de réussite -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-pie me-2"></i>
                        Success Rate Distribution
                    </h4>
                </div>
                <div class="card-body">
                    <canvas id="successRateChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-info-circle me-2"></i>
                        Key Insights
                    </h4>
                </div>
                <div class="card-body">
                    <div class="insights">
                        @php
                            $successRate = $stats['total_attempts'] > 0 ? ($stats['successful_attempts'] / $stats['total_attempts']) * 100 : 0;
                        @endphp
                        
                        <div class="mb-3">
                            <h6 class="fw-medium">Overall Success Rate</h6>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-success" style="width: {{ $successRate }}%">
                                    {{ number_format($successRate, 1) }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $stats['successful_attempts'] }} out of {{ $stats['total_attempts'] }} attempts</small>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-medium">Average Performance</h6>
                            <div class="d-flex justify-content-between">
                                <span>Average Score:</span>
                                <span class="fw-medium">{{ number_format($stats['average_score'], 1) }}%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Total Students:</span>
                                <span class="fw-medium">{{ $stats['unique_students'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Active Quizzes:</span>
                                <span class="fw-medium">{{ $stats['unique_quizzes'] }}</span>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fa fa-lightbulb me-2"></i>
                            <strong>Tip:</strong> Students with 3+ attempts are considered for top rankings to ensure statistical relevance.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Statistics Chart
    const dailyCtx = document.getElementById('dailyStatsChart').getContext('2d');
    const dailyData = @json($dailyStats);
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Quiz Attempts',
                data: dailyData.map(item => item.attempts),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Average Score',
                data: dailyData.map(item => item.avg_score),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Success Rate Chart
    const successCtx = document.getElementById('successRateChart').getContext('2d');
    
    new Chart(successCtx, {
        type: 'doughnut',
        data: {
            labels: ['Successful', 'Failed'],
            datasets: [{
                data: [{{ $stats['successful_attempts'] }}, {{ $stats['failed_attempts'] }}],
                backgroundColor: [
                    'rgb(40, 167, 69)',
                    'rgb(220, 53, 69)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
