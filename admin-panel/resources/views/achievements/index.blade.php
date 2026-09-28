@extends('layouts.app')

@section('content') 
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="award" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Academy Achievements</h4>
                                <p class="text-white-50 mb-0">View and manage academy statistics</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.achievements.edit') }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-2"></i>
                            Edit Statistics
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle" class="me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Achievements Cards -->
                    <div class="row g-4">
                        <!-- Training Programs -->
                        <div class="col-xl-3 col-md-6">
                            <div class="achievement-card">
                                <div class="achievement-icon bg-primary">
                                    <i data-feather="book-open"></i>
                                </div>
                                <div class="achievement-content">
                                    <h3 class="achievement-number">{{ $achievement->formatted_training_programs }}</h3>
                                    <p class="achievement-label">Training Programs</p>
                                </div>
                            </div>
                        </div>

                        <!-- Registered Students -->
                        <div class="col-xl-3 col-md-6">
                            <div class="achievement-card">
                                <div class="achievement-icon bg-success">
                                    <i data-feather="users"></i>
                                </div>
                                <div class="achievement-content">
                                    <h3 class="achievement-number">{{ $achievement->formatted_registered_students }}</h3>
                                    <p class="achievement-label">Registered Students</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academy Books -->
                        <div class="col-xl-3 col-md-6">
                            <div class="achievement-card">
                                <div class="achievement-icon bg-info">
                                    <i data-feather="book"></i>
                                </div>
                                <div class="achievement-content">
                                    <h3 class="achievement-number">{{ $achievement->formatted_academy_books }}</h3>
                                    <p class="achievement-label">Academy Books</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ready Instructors -->
                        <div class="col-xl-3 col-md-6">
                            <div class="achievement-card">
                                <div class="achievement-icon bg-warning">
                                    <i data-feather="user-check"></i>
                                </div>
                                <div class="achievement-content">
                                    <h3 class="achievement-number">{{ $achievement->formatted_ready_instructors }}</h3>
                                    <p class="achievement-label">Ready Instructors</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Statistics Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="50%">Metric</th>
                                                    <th width="50%">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <i data-feather="book-open" class="me-2 text-primary"></i>
                                                        <strong>Training Programs</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                            {{ $achievement->formatted_training_programs }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i data-feather="users" class="me-2 text-success"></i>
                                                        <strong>Registered Students</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                            {{ $achievement->formatted_registered_students }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i data-feather="book" class="me-2 text-info"></i>
                                                        <strong>Academy Books</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                            {{ $achievement->formatted_academy_books }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i data-feather="user-check" class="me-2 text-warning"></i>
                                                        <strong>Ready Instructors</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                            {{ $achievement->formatted_ready_instructors }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i data-feather="info" class="me-2"></i>
                                <strong>Note:</strong> These statistics represent the academy's key achievements and are displayed on the homepage. Last updated: {{ $achievement->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.achievement-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.achievement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.achievement-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.achievement-icon i {
    width: 30px;
    height: 30px;
    color: white;
}

.achievement-content {
    flex: 1;
}

.achievement-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.achievement-label {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
    font-weight: 500;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6e6b7b;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection

@section('scripts')
<script>
// Initialize Feather icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection

