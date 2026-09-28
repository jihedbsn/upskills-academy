<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i data-feather="mail" class="me-2"></i>
            Communication History
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('emails.index', ['email' => $student->email]) }}" class="btn btn-sm btn-outline-primary">
                <i data-feather="send" class="me-1" style="width:14px;height:14px;"></i>
                Send Email
            </a>
            <a href="{{ route('email-logs.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-secondary">
                View all logs
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($emailLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Date & Time</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emailLogs as $log)
                            <tr>
                                <td>
                                    @if($log->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->type_badge }}">{{ $log->type_label }}</span>
                                </td>
                                <td>{{ Str::limit($log->subject, 60) }}</td>
                                <td>
                                    <div>{{ $log->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if(filled($log->body))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#studentEmailLogModal{{ $log->id }}">
                                            View
                                        </button>
                                    @elseif($log->status === 'failed' && $log->error_message)
                                        <small class="text-danger">{{ Str::limit($log->error_message, 80) }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($emailLogs as $log)
                @if(filled($log->body))
                    <div class="modal fade" id="studentEmailLogModal{{ $log->id }}" tabindex="-1"
                         aria-labelledby="studentEmailLogLabel{{ $log->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="studentEmailLogLabel{{ $log->id }}">{{ $log->subject }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <dl class="row small mb-0">
                                        <dt class="col-sm-3 text-muted">Type</dt>
                                        <dd class="col-sm-9">{{ $log->type_label }}</dd>
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
        @else
            <div class="text-center py-4">
                <i data-feather="inbox" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                <p class="text-muted mb-0">No emails logged for this student yet.</p>
            </div>
        @endif
    </div>
</div>
