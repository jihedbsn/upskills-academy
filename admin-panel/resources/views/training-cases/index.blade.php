@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Training Cases</h4>
                                <p class="text-white-50 mb-0">Manage practical exam training scenarios</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('training-cases.create') }}" class="btn btn-success">
                                <i data-feather="plus" class="me-1"></i> Add Training Case
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i data-feather="check-circle" class="me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($trainingCases->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                        <h5>No Training Cases Yet</h5>
                        <p class="text-muted">Start by adding your first training case for practical exams</p>
                        <a href="{{ route('training-cases.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add First Training Case
                        </a>
                    </div>
                </div>
            @else
                <!-- Training Cases List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Files</th>
                                        <th>Used in Courses</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainingCases as $case)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $case->name }}</strong></td>
                                        <td>{{ Str::limit($case->description, 50) }}</td>
                                        <td>
                                            @if($case->files_count > 0)
                                                <span class="badge bg-primary">
                                                    <i data-feather="file" style="width: 14px; height: 14px;"></i>
                                                    {{ $case->files_count }} {{ Str::plural('file', $case->files_count) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No files</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($case->products_count > 0)
                                                <span class="badge bg-info">{{ $case->products_count }} {{ Str::plural('course', $case->products_count) }}</span>
                                            @else
                                                <span class="text-muted">Not used</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('training-cases.toggle-status', $case->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $case->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $case->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $case->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('training-cases.edit', $case->id) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <form action="{{ route('training-cases.destroy', $case->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this training case and all its files?');"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
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
                        <div class="d-flex justify-content-center mt-3">
                            {{ $trainingCases->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
