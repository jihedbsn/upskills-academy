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
                                    <i data-feather="bar-chart-2" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Homepage Statistics Management</h4>
                                <p class="text-white-50 mb-0">Manage statistics displayed on the homepage</p>
                            </div>
                        </div>
                        <a href="{{ route('accueil-chiffres.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            Add Statistics
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

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistics table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Coach Ready</th>
                                    <th>Book of Academy</th>
                                    <th>Registered Students</th>
                                    <th>Training Programs</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accueilChiffres as $accueilChiffre)
                                    <tr>
                                        <td>{{ $accueilChiffre->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($accueilChiffre->icone_coach_ready)
                                                    <img src="{{ $accueilChiffre->icone_coach_ready_url }}" alt="Coach Ready Icon" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <span class="badge bg-primary">{{ $accueilChiffre->coach_ready }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($accueilChiffre->icone_book_of_the_academy)
                                                    <img src="{{ $accueilChiffre->icone_book_of_the_academy_url }}" alt="Book Icon" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <span class="badge bg-success">{{ $accueilChiffre->book_of_the_academy }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($accueilChiffre->icone_registered_student)
                                                    <img src="{{ $accueilChiffre->icone_registered_student_url }}" alt="Student Icon" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <span class="badge bg-info">{{ $accueilChiffre->registered_student }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($accueilChiffre->icone_training_program)
                                                    <img src="{{ $accueilChiffre->icone_training_program_url }}" alt="Training Icon" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <span class="badge bg-warning">{{ $accueilChiffre->training_program }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($accueilChiffre->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $accueilChiffre->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('accueil-chiffres.show', $accueilChiffre) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('accueil-chiffres.edit', $accueilChiffre) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('accueil-chiffres.toggle', $accueilChiffre) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $accueilChiffre->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $accueilChiffre->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i data-feather="{{ $accueilChiffre->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('accueil-chiffres.destroy', $accueilChiffre) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete these statistics?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="bar-chart-2" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>No statistics found</p>
                                                <a href="{{ route('accueil-chiffres.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Add the first statistics
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($accueilChiffres->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $accueilChiffres->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.btn-group .btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
