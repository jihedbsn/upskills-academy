@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="book-open" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Subjects Management</h1>
                                <p class="text-muted mb-0">Manage exam subjects in Arabic and English</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('sujets.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Add Subject
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i data-feather="book" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <small>Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i data-feather="user-check" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['fa'] }}</h4>
                                    <small>FA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i data-feather="user-plus" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['fi'] }}</h4>
                                    <small>FI</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i data-feather="award" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['pt'] }}</h4>
                                    <small>PT</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <i data-feather="more-horizontal" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['autres'] }}</h4>
                                    <small>Autres</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body text-center">
                                    <i data-feather="globe" class="fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $stats['arabic'] + $stats['english'] }}</h4>
                                    <small>Languages</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i data-feather="filter" class="me-2"></i>
                                        Filters
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('sujets.index') }}" class="row">
                                        <div class="col-md-3">
                                            <label for="type_filter" class="form-label">Type</label>
                                            <select class="form-select" id="type_filter" name="type">
                                                <option value="">All Types</option>
                                                <option value="fa" {{ request('type') == 'fa' ? 'selected' : '' }}>Foundation English (FA)</option>
                                                <option value="fi" {{ request('type') == 'fi' ? 'selected' : '' }}>Fluency &amp; Interaction (FI)</option>
                                                <option value="pt" {{ request('type') == 'pt' ? 'selected' : '' }}>Proficiency Track (PT)</option>
                                                <option value="autres" {{ request('type') == 'autres' ? 'selected' : '' }}>Autres</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="lang_filter" class="form-label">Language</label>
                                            <select class="form-select" id="lang_filter" name="lang">
                                                <option value="">All Languages</option>
                                                <option value="ar" {{ request('lang') == 'ar' ? 'selected' : '' }}>العربية</option>
                                                <option value="en" {{ request('lang') == 'en' ? 'selected' : '' }}>English</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="search_filter" class="form-label">Search</label>
                                            <input type="text" class="form-control" id="search_filter" name="search"
                                                   placeholder="Search in descriptions..." value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i data-feather="search"></i>
                                                </button>
                                                <a href="{{ route('sujets.index') }}" class="btn btn-outline-secondary">
                                                    <i data-feather="refresh-cw"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des sujets -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="me-2"></i>
                                        <h4 class="card-title mb-1">Liste des Sujets</h4>
                                        <p class="text-white-50 mb-0 ms-3">Gérer tous les sujets d'examen</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if($sujets->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover mb-0">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="10%">Type</th>
                                                        <th width="10%">Language</th>
                                                        <th width="55%">Description</th>
                                                        <th width="10%">Created</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sujets as $sujet)
                                                        <tr>
                                                            <td>{{ $sujet->id }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $sujet->type_color }}">
                                                                    {{ $sujet->type_name }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $sujet->lang === 'ar' ? 'bg-success' : 'bg-info' }}">
                                                                    {{ $sujet->language_name }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="text-wrap" style="max-width: 500px;">
                                                                    {{ $sujet->short_description }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    {{ $sujet->created_at->format('d/m/Y') }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('sujets.show', $sujet) }}"
                                                                       class="btn btn-sm btn-outline-info" title="View">
                                                                        <i data-feather="eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('sujets.edit', $sujet) }}"
                                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                                        <i data-feather="edit"></i>
                                                                    </a>
                                                                    <form action="{{ route('sujets.destroy', $sujet) }}"
                                                                          method="POST" class="d-inline"
                                                                          onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                            <i data-feather="trash-2"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Pagination -->
                                        @if($sujets->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            <nav aria-label="Subjects pagination">
                                                <ul class="pagination">
                                                    {{-- Previous Page Link --}}
                                                    @if ($sujets->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link">&laquo;</span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $sujets->previousPageUrl() }}" rel="prev">&laquo;</a>
                                                        </li>
                                                    @endif

                                                    {{-- Pagination Elements --}}
                                                    @foreach ($sujets->getUrlRange(1, $sujets->lastPage()) as $page => $url)
                                                        @if ($page == $sujets->currentPage())
                                                            <li class="page-item active">
                                                                <span class="page-link">{{ $page }}</span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach

                                                    {{-- Next Page Link --}}
                                                    @if ($sujets->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $sujets->nextPageUrl() }}" rel="next">&raquo;</a>
                                                        </li>
                                                    @else
                                                        <li class="page-item disabled">
                                                            <span class="page-link">&raquo;</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-center py-5">
                                            <i data-feather="book-open" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
                                            <h5 class="text-muted">No subjects found</h5>
                                            <p class="text-muted">Start by creating your first subject</p>
                                            <a href="{{ route('sujets.create') }}" class="btn btn-primary">
                                                <i data-feather="plus" class="me-2"></i>
                                                Create Subject
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();

    // Auto-submit filter form when type or language changes
    document.addEventListener('DOMContentLoaded', function() {
        const typeFilter = document.getElementById('type_filter');
        const langFilter = document.getElementById('lang_filter');
        const filterForm = typeFilter.closest('form');

        // Submit form when type filter changes
        typeFilter.addEventListener('change', function() {
            filterForm.submit();
        });

        // Submit form when language filter changes
        langFilter.addEventListener('change', function() {
            filterForm.submit();
        });
    });
</script>
@endpush

<style>
.card.bg-primary, .card.bg-success, .card.bg-info {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.card.bg-primary:hover, .card.bg-success:hover, .card.bg-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.text-wrap {
    word-wrap: break-word;
    word-break: break-word;
}
</style>
