@extends('layouts.app')

@section('title', 'Chatbot Support - Admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>🤖 Chatbot Support Requests</h3>
                    <p class="text-muted">Questions envoyées par les utilisateurs via le chatbot</p>
                </div>
                <div class="card-body">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>
                                            @if($ticket->student)
                                                {{ $ticket->student->name }}
                                                <br><small class="text-muted">{{ $ticket->student->email }}</small>
                                            @else
                                                <span class="text-muted">Anonymous</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#messageModal{{ $ticket->id }}">
                                                View Message
                                            </button>
                                        </td>
                                        <td>
                                            @if($ticket->status === 'open')
                                                <span class="badge bg-warning">Open</span>
                                            @elseif($ticket->status === 'in_progress')
                                                <span class="badge bg-info">In Progress</span>
                                            @elseif($ticket->status === 'closed')
                                                <span class="badge bg-success">Closed</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="updateStatus({{ $ticket->id }}, 'in_progress')">
                                                    In Progress
                                                </button>
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="updateStatus({{ $ticket->id }}, 'closed')">
                                                    Close
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal for message details -->
                                    <div class="modal fade" id="messageModal{{ $ticket->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Support Request Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Subject:</h6>
                                                    <p>{{ $ticket->subject }}</p>
                                                    
                                                    <h6>Message:</h6>
                                                    <div class="bg-light p-3 rounded">
                                                        <pre style="white-space: pre-wrap;">{{ $ticket->message }}</pre>
                                                    </div>
                                                    
                                                    @if($ticket->responses->count() > 0)
                                                        <h6 class="mt-3">Internal Notes:</h6>
                                                        @foreach($ticket->responses as $response)
                                                            @if($response->is_internal)
                                                                <div class="bg-info bg-opacity-10 p-2 rounded mb-2">
                                                                    <small class="text-muted">{{ $response->created_at->format('M d, Y H:i') }}</small>
                                                                    <pre style="white-space: pre-wrap; margin: 0;">{{ $response->message }}</pre>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h4>No support requests yet</h4>
                            <p class="text-muted">When users send questions via the chatbot, they will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(ticketId, status) {
    if (confirm('Are you sure you want to update the status?')) {
        fetch(`/admin/tickets/${ticketId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    }
}
</script>
@endsection
