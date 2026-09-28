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
                                <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="file" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Resources Management</h4>
                                <p class="text-white-50 mb-0">Manage all files and resources</p>
                            </div>
                        </div>
                        <a href="{{ route('resources.create') }}" class="btn btn-warning">
                            <i data-feather="plus" class="me-2"></i>
                            New Resource
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('resources.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or file..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Per Page</label>
                                <select name="per_page" class="form-select">
                                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i data-feather="filter" class="me-2" style="width: 14px; height: 14px;"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        @if(request()->hasAny(['search', 'type']))
                            <div class="mt-3">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="text-muted small">Active filters:</span>
                                    @if(request('search'))
                                        <span class="badge bg-info">
                                            Search: {{ request('search') }}
                                            <a href="{{ route('resources.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                    @if(request('type'))
                                        <span class="badge bg-warning">
                                            Type: {{ ucfirst(request('type')) }}
                                            <a href="{{ route('resources.index', array_merge(request()->except('type'), ['page' => 1])) }}" class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- End Search and Filter Section -->

                    @if($resources->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name (Arabic)</th>
                                        <th>Name (English)</th>
                                        <th>Type</th>
                                        <th>File</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resources as $resource)
                                        <tr>
                                            <td>{{ $resource->id }}</td>
                                            <td>
                                                <span class="fw-medium">{{ $resource->name_ar ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $resource->name_en ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($resource->type == 'video')
                                                    <span class="badge bg-danger">
                                                        <i data-feather="video" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Video
                                                    </span>
                                                @elseif($resource->type == 'book')
                                                    <span class="badge bg-success">
                                                        <i data-feather="book" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Book
                                                    </span>
                                                @elseif($resource->type == 'audio')
                                                    <span class="badge bg-warning">
                                                        <i data-feather="music" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Audio
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $resource->type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="file" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ basename($resource->file) }}</span>
                                                </div>
                                                @if($resource->type === 'video' && $resource->video_files && count($resource->video_files) > 0)
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            <i data-feather="video" class="me-1" style="width: 12px; height: 12px;"></i>
                                                            {{ count($resource->video_files) }} video file(s)
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $resource->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('resources.show', $resource) }}" class="btn btn-sm btn-outline-info">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('resources.download', $resource) }}" class="btn btn-sm btn-outline-success">
                                                        <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-outline-warning">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $resource->id }}" action="{{ route('resources.destroy', $resource) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteResource({{ $resource->id }})">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $resources->firstItem() ?? 0 }} to {{ $resources->lastItem() ?? 0 }} of {{ $resources->total() }} results
                            </div>
                            <div>
                                {{ $resources->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="file" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No resources found</h5>
                            <p class="text-muted">Start by adding your first resource</p>
                            <a href="{{ route('resources.create') }}" class="btn btn-warning">
                                <i data-feather="plus" class="me-2"></i>
                                Add a resource
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.deleteResource = function(resourceId) {
    console.log('Delete button clicked for resource:', resourceId);

    if (confirm('Êtes-vous sûr de vouloir supprimer cette ressource ? Cette action est irréversible.')) {
        const form = document.getElementById('delete-form-' + resourceId);
        console.log('Form found:', form);

        if (form) {
            console.log('Submitting form...');
            form.submit();
        } else {
            console.error('Form not found for resource ID:', resourceId);
            alert('Erreur: formulaire non trouvé');
        }
    }
}

console.log('deleteResource function loaded');
</script>
@endpush
