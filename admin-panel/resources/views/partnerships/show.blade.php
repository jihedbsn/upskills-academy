@extends('layouts.app')

@section('title', 'Partnership Details - ' . $partnership->institution_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('partnerships.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <span class="h4 mb-0">Partnership Details</span>
                    </div>
                    <span class="badge bg-{{ $partnership->status_badge_class }} fs-6">
                        {{ ucfirst($partnership->status) }}
                    </span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Left Column - Institution Info -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fa fa-building me-2"></i>
                                        Institution Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px;">Institution Name</th>
                                            <td><strong>{{ $partnership->institution_name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                <a href="mailto:{{ $partnership->email }}">{{ $partnership->email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>
                                                <a href="tel:{{ $partnership->phone }}">{{ $partnership->phone }}</a>
                                            </td>
                                        </tr>
                                        @if($partnership->website)
                                            <tr>
                                                <th>Website</th>
                                                <td>
                                                    <a href="{{ $partnership->website }}" target="_blank">
                                                        {{ $partnership->website }} <i class="fa fa-external-link-alt ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $partnership->institution_address }}</td>
                                        </tr>
                                        <tr>
                                            <th>Submitted</th>
                                            <td>{{ $partnership->created_at->format('F d, Y \a\t H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Profile File -->
                            @if($partnership->profile_file)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fa fa-file me-2"></i>
                                            Institution Profile File
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="fa fa-file-pdf fa-2x text-danger me-3"></i>
                                                <span>{{ basename($partnership->profile_file) }}</span>
                                            </div>
                                            <a href="{{ route('partnerships.download', $partnership) }}" class="btn btn-primary">
                                                <i class="fa fa-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column - Courses & Status -->
                        <div class="col-md-6">
                            <!-- Requested Courses -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fa fa-book me-2"></i>
                                        Requested Courses
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($partnership->requested_courses && count($partnership->requested_courses) > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($partnership->courses_list as $course)
                                                <li class="list-group-item d-flex align-items-center">
                                                    <i class="fa fa-check-circle text-success me-2"></i>
                                                    {{ $course }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">No specific courses selected.</p>
                                    @endif

                                    @if($partnership->additional_courses)
                                        <hr>
                                        <h6 class="text-muted">Additional Courses Request:</h6>
                                        <p class="mb-0 bg-light p-3 rounded">{{ $partnership->additional_courses }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Update Status Form -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fa fa-edit me-2"></i>
                                        Update Status
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('partnerships.update-status', $partnership) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="pending" {{ $partnership->status == 'pending' ? 'selected' : '' }}>
                                                    ⏳ Pending
                                                </option>
                                                <option value="approved" {{ $partnership->status == 'approved' ? 'selected' : '' }}>
                                                    ✓ Approved
                                                </option>
                                                <option value="rejected" {{ $partnership->status == 'rejected' ? 'selected' : '' }}>
                                                    ✗ Rejected
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes (will be sent to the institution)</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                                      placeholder="Add any notes or feedback for the institution...">{{ $partnership->notes }}</textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save me-1"></i> Update Status & Send Notification
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Section -->
                    <div class="card border-danger mt-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                Danger Zone
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Once you delete a partnership request, there is no going back. Please be certain.</p>
                            <form action="{{ route('partnerships.destroy', $partnership) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this partnership request? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa fa-trash me-1"></i> Delete Partnership Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
