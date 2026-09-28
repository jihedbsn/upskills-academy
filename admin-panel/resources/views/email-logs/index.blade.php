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
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="mail" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title mb-0">Unified Email Log</h1>
                                <small class="text-muted">Complete audit trail of all outbound emails to students — manual admin messages, welcome emails, password resets, payment reminders, birthday greetings, and every other automated notification.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @if(!empty($filteredStudent))
                        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                            <span>
                                Showing email history for
                                <strong>{{ $filteredStudent->first_name }} {{ $filteredStudent->last_name }}</strong>
                                ({{ $filteredStudent->email }})
                            </span>
                            <a href="{{ route('students.show', $filteredStudent) }}" class="btn btn-sm btn-outline-primary">Student profile</a>
                        </div>
                    @endif

                    {{-- Statistics Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 small text-white-50">Total Emails</p>
                                            <h3 class="mb-0 text-white">{{ number_format($stats['total']) }}</h3>
                                        </div>
                                        <i data-feather="mail" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 small text-white-50">Delivered</p>
                                            <h3 class="mb-0 text-white">{{ number_format($stats['sent']) }}</h3>
                                        </div>
                                        <i data-feather="check-circle" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-gradient-danger text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 small text-white-50">Failed</p>
                                            <h3 class="mb-0 text-white">{{ number_format($stats['failed']) }}</h3>
                                        </div>
                                        <i data-feather="x-circle" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 small text-white-50">Today</p>
                                            <h3 class="mb-0 text-white">{{ number_format($stats['today']) }}</h3>
                                        </div>
                                        <i data-feather="clock" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card border mb-4">
                        <div class="card-body py-3">
                            <form method="GET" action="{{ route('email-logs.index') }}">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Search</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i data-feather="search" style="width:14px;height:14px;"></i></span>
                                            <input type="text" name="search" class="form-control" placeholder="Email, name, subject..." value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Email Type</label>
                                        <select name="type" class="form-select">
                                            <option value="">All Types</option>
                                            @foreach($emailTypes as $type)
                                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Delivery</label>
                                        <select name="status" class="form-select">
                                            <option value="">All</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>✅ Sent</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ Failed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">Opens (custom email)</label>
                                        <select name="read_status" class="form-select">
                                            <option value="">Any</option>
                                            <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Not opened yet</option>
                                            <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Opened</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold">From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label small fw-bold">To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i data-feather="filter" style="width:14px;height:14px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Email Logs Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i data-feather="list" class="me-2"></i>
                                    <h4 class="card-title mb-0 text-white">Email Log</h4>
                                    <span class="badge bg-light text-dark ms-2">{{ $logs->total() }} records</span>
                                </div>
                                @if(request()->hasAny(['search', 'type', 'status', 'read_status', 'date_from', 'date_to', 'student_id']))
                                    <a href="{{ route('email-logs.index') }}" class="btn btn-sm btn-outline-light">
                                        <i data-feather="x" style="width:14px;height:14px;" class="me-1"></i>Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($logs->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Status</th>
                                                <th>Student</th>
                                                <th>Type</th>
                                                <th>Subject</th>
                                                <th>Date & Time</th>
                                                <th>Open status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($logs as $log)
                                                <tr>
                                                    <td class="text-muted">{{ $log->id }}</td>
                                                    <td>
                                                        @if($log->status === 'sent')
                                                            <span class="badge bg-success">
                                                                <i data-feather="check" style="width:12px;height:12px;" class="me-1"></i>Sent
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i data-feather="x" style="width:12px;height:12px;" class="me-1"></i>Failed
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $log->student_name ?? 'N/A' }}</strong>
                                                        </div>
                                                        <small class="text-muted">{{ $log->student_email }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $log->type_badge }}">
                                                            {{ $log->type_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span title="{{ $log->subject }}">
                                                            {{ Str::limit($log->subject, 50) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                                    </td>
                                                    <td>
                                                        @if($log->tracksOpens())
                                                            @if($log->read_at)
                                                                <span class="badge bg-success">Opened</span>
                                                                <div><small class="text-muted">{{ $log->read_at->format('M d, Y H:i') }}</small></div>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Not opened</span>
                                                                <div><small class="text-muted d-block mt-1" title="Gmail and many clients block remote images until enabled. The Send Email template also includes a “Confirm delivery” link that records an open when clicked.">Opens when images load or recipient uses the confirm link in the message.</small></div>
                                                            @endif
                                                        @elseif($log->email_type === 'custom_email' && filled($log->body))
                                                            <span class="text-muted small">—</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="white-space-normal">
                                                        @if(filled($log->body))
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-primary mb-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#emailLogBodyModal{{ $log->id }}">
                                                                <i data-feather="mail" style="width:14px;height:14px;" class="me-1"></i>
                                                                View email
                                                            </button>
                                                        @endif
                                                        @if($log->status === 'failed' && $log->error_message)
                                                            <button type="button" class="btn btn-sm btn-outline-danger mb-1"
                                                                    data-bs-toggle="popover"
                                                                    data-bs-trigger="hover"
                                                                    data-bs-placement="left"
                                                                    data-bs-content="{{ Str::limit($log->error_message, 200) }}"
                                                                    title="Error Details">
                                                                <i data-feather="alert-circle" style="width:14px;height:14px;"></i>
                                                                Error
                                                            </button>
                                                        @endif
                                                        @if(!filled($log->body))
                                                            @if($log->related_model && $log->related_id)
                                                                <small class="text-muted d-block">
                                                                    {{ class_basename($log->related_model) }} #{{ $log->related_id }}
                                                                </small>
                                                            @elseif(!($log->status === 'failed' && $log->error_message))
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @foreach($logs as $log)
                                    @if(filled($log->body))
                                        <div class="modal fade" id="emailLogBodyModal{{ $log->id }}" tabindex="-1"
                                             aria-labelledby="emailLogBodyLabel{{ $log->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="emailLogBodyLabel{{ $log->id }}">{{ $log->subject }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <dl class="row small mb-0">
                                                            <dt class="col-sm-3 text-muted">To</dt>
                                                            <dd class="col-sm-9">{{ $log->student_email }}</dd>
                                                            <dt class="col-sm-3 text-muted">Sent</dt>
                                                            <dd class="col-sm-9">{{ $log->created_at->format('M d, Y H:i:s') }}</dd>
                                                        </dl>
                                                        <hr>
                                                        <div class="lh-lg" style="white-space: pre-wrap;">{{ $log->body }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-center p-3">
                                    {{ $logs->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i data-feather="inbox" style="width: 48px; height: 48px; color: #ccc;"></i>
                                    <h5 class="mt-3 text-muted">No email logs found</h5>
                                    <p class="text-muted">Email delivery logs will appear here once emails are sent.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize popovers for error details
    document.addEventListener('DOMContentLoaded', function() {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>
@endpush
@endsection
