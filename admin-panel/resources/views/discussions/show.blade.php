@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Discussion Details</h4>
                <div>
                    <a href="{{ route('admin.discussions.index') }}" class="btn btn-secondary btn-sm">
                        <i data-feather="arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.discussions.edit', $discussion) }}" class="btn btn-primary btn-sm">
                        <i data-feather="edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Student Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Student Information</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">Student:</td>
                                        <td>
                                            @if($discussion->student)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-content">{{ $discussion->student->initials }}</span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $discussion->student->full_name }}</strong><br>
                                                        <small class="text-muted">{{ $discussion->student->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Student Deleted</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Course:</td>
                                        <td>
                                            @if($discussion->product)
                                                <span class="badge bg-primary">{{ $discussion->product->titre }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status:</td>
                                        <td>
                                            @if($discussion->is_approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Created:</td>
                                        <td>{{ $discussion->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-3">
                            <h6>Quick Actions</h6>
                            @if($discussion->is_approved)
                                <form action="{{ route('admin.discussions.disapprove', $discussion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i data-feather="x-circle"></i> Disapprove
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.discussions.approve', $discussion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i data-feather="check-circle"></i> Approve
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.discussions.destroy', $discussion) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i data-feather="trash-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Discussion Content -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Discussion Comment</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $discussion->commentaire }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responses Section -->
                @if($discussion->responses && $discussion->responses->count() > 0)
                <hr class="my-4">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3">Admin Responses ({{ $discussion->responses->count() }})</h5>
                        
                        @foreach($discussion->responses as $response)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center">
                                        @if($response->admin)
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-content">{{ $response->admin->initials }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $response->admin->full_name }}</h6>
                                            <small class="text-muted">{{ $response->admin->email }}</small>
                                        </div>
                                        @else
                                        <div>
                                            <h6 class="mb-0 text-muted">Admin Deleted</h6>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($response->is_approved)
                                            <span class="badge bg-success mb-2">Approved</span>
                                        @else
                                            <span class="badge bg-warning mb-2">Pending</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $response->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-0 mt-2">{{ $response->reponse }}</p>
                                
                                <div class="mt-3">
                                    @if($response->is_approved)
                                        <form action="{{ route('admin.response-discussions.disapprove', $response) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i data-feather="x-circle"></i> Disapprove
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.response-discussions.approve', $response) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i data-feather="check-circle"></i> Approve
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('admin.response-discussions.edit', $response) }}" class="btn btn-primary btn-sm">
                                        <i data-feather="edit"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.response-discussions.destroy', $response) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this response?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i data-feather="trash-2"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <hr class="my-4">
                <div class="alert alert-info">
                    <i data-feather="info"></i> No admin responses yet for this discussion.
                </div>
                @endif

                <!-- Add Response Button -->
                <div class="mt-3">
                    <a href="{{ route('admin.response-discussions.create', ['discussion_id' => $discussion->id]) }}" class="btn btn-primary">
                        <i data-feather="plus"></i> Add Admin Response
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
@endpush
@endsection
