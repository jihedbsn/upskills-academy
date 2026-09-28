@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.support_and_ticket'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.support_and_ticket') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.support_and_ticket') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.get_help_support') }}</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="bi bi-plus-circle me-2"></i>{{ __('student_dashboard.create_ticket') }}
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-ticket"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $supportStats['total_tickets'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.total_tickets') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $supportStats['resolved_tickets'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.resolved_tickets') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $supportStats['open_tickets'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.pending_tickets') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-chat"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $supportStats['this_month'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.this_month') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Help Center -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student_dashboard.help_center') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>{{ __('student_dashboard.frequently_asked_questions') }}</h6>
                        <p class="text-muted mb-3">{{ __('student_dashboard.faq_description') }}</p>
                        <a href="/{{ app()->getLocale() }}/pages/faq" class="btn btn-outline-primary w-100">
                            <i class="bi bi-question-circle me-2"></i>{{ __('student_dashboard.view_faq') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student_dashboard.recent_tickets') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('student_dashboard.ticket_id') }}</th>
                                    <th>{{ __('student_dashboard.subject') }}</th>
                                    <th>{{ __('student_dashboard.status') }}</th>
                                    <th>{{ __('student_dashboard.priority') }}</th>
                                    <th>{{ __('student_dashboard.created') }}</th>
                                    <th>{{ __('student_dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>#TKT-{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $ticket->sujet }}</td>
                                        <td>
                                            @if($ticket->ticket_iscomplet)
                                                <span class="badge bg-success">{{ __('student_dashboard.resolved') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('student_dashboard.in_progress') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ __('student_dashboard.medium') }}</span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ticketModal"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-subject="{{ $ticket->sujet }}"
                                                    data-ticket-status="{{ $ticket->ticket_iscomplet ? 'resolved' : 'in_progress' }}"
                                                    data-ticket-created="{{ $ticket->created_at->format('d M Y, h:i A') }}"
                                                    data-ticket-updated="{{ $ticket->updated_at->format('d M Y, h:i A') }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-ticket display-4 text-muted"></i>
                                            <p class="mt-2 text-muted">{{ __('student_dashboard.no_tickets_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.create_ticket') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('student-dashboard.support.tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.subject') }} *</label>
                        <input type="text" name="sujet" class="form-control" placeholder="{{ __('student_dashboard.enter_subject') }}" required>
                    </div>
                                         <div class="mb-3">
                         <label class="form-label">{{ __('student_dashboard.description') }} *</label>
                         <textarea name="message" class="form-control" rows="5" placeholder="{{ __('student_dashboard.describe_your_issue') }}" required></textarea>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                         <button type="submit" class="btn btn-primary">{{ __('student_dashboard.submit_ticket') }}</button>
                     </div>
                 </form>
             </div>
        </div>
    </div>
</div>

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.ticket_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>{{ __('student_dashboard.ticket_info') }}</h6>
                        <ul class="list-unstyled">
                            <li><strong>{{ __('student_dashboard.ticket_id') }}:</strong> <span id="ticket-id">#TKT-001</span></li>
                            <li><strong>{{ __('student_dashboard.status') }}:</strong> <span id="ticket-status" class="badge bg-success">{{ __('student_dashboard.resolved') }}</span></li>
                            <li><strong>{{ __('student_dashboard.priority') }}:</strong> <span class="badge bg-warning">{{ __('student_dashboard.medium') }}</span></li>
                            <li><strong>{{ __('student_dashboard.created') }}:</strong> <span id="ticket-created">15 Jan 2024</span></li>
                            <li><strong>{{ __('student_dashboard.updated') }}:</strong> <span id="ticket-updated">18 Jan 2024</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>{{ __('student_dashboard.subject') }}</h6>
                        <p id="ticket-subject">{{ __('student_dashboard.course_access_issue') }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>{{ __('student_dashboard.conversation') }}</h6>
                    <div id="ticket-conversation" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        <div class="text-center text-muted">
                            <i class="bi bi-chat-dots"></i>
                            <p>{{ __('student_dashboard.loading_conversation') }}</p>
                        </div>
                    </div>
                </div>

                <form id="reply-form" action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.add_reply') }}</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="{{ __('student_dashboard.type_your_reply') }}" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('student_dashboard.send_reply') }}</button>
                        <button type="button" id="close-ticket-btn" class="btn btn-success" style="display: none;">
                            <i class="bi bi-check-circle me-2"></i>{{ __('student_dashboard.close_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer l'ouverture du modal de détails du ticket
    const ticketModal = document.getElementById('ticketModal');
    if (ticketModal) {
        ticketModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const ticketId = button.getAttribute('data-ticket-id');
            const ticketSubject = button.getAttribute('data-ticket-subject');
            const ticketStatus = button.getAttribute('data-ticket-status');
            const ticketCreated = button.getAttribute('data-ticket-created');
            const ticketUpdated = button.getAttribute('data-ticket-updated');

            // Mettre à jour les informations du ticket
            document.getElementById('ticket-id').textContent = '#TKT-' + ticketId.padStart(3, '0');
            document.getElementById('ticket-subject').textContent = ticketSubject;
            document.getElementById('ticket-created').textContent = ticketCreated;
            document.getElementById('ticket-updated').textContent = ticketUpdated;

            // Mettre à jour le statut
            const statusElement = document.getElementById('ticket-status');
            if (ticketStatus === 'resolved') {
                statusElement.className = 'badge bg-success';
                statusElement.textContent = '{{ __("student_dashboard.resolved") }}';
            } else {
                statusElement.className = 'badge bg-warning';
                statusElement.textContent = '{{ __("student_dashboard.in_progress") }}';
            }

            // Mettre à jour l'action du formulaire
            document.getElementById('reply-form').action = '{{ route("student-dashboard.support.tickets.reply", ":ticket") }}'.replace(':ticket', ticketId);

            // Gérer l'affichage du bouton "Fermer le ticket"
            const closeTicketBtn = document.getElementById('close-ticket-btn');
            if (ticketStatus === 'resolved') {
                closeTicketBtn.style.display = 'none';
            } else {
                closeTicketBtn.style.display = 'inline-block';
                closeTicketBtn.onclick = function() {
                    if (confirm('{{ __("student_dashboard.confirm_close_ticket") }}')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("student-dashboard.support.tickets.close", ":ticket") }}'.replace(':ticket', ticketId);

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                };
            }

            // Charger les réponses du ticket
            loadTicketResponses(ticketId);
        });
    }

    // Fonction pour charger les réponses du ticket
    function loadTicketResponses(ticketId) {
        const conversationDiv = document.getElementById('ticket-conversation');
        conversationDiv.innerHTML = '<div class="text-center text-muted"><i class="bi bi-chat-dots"></i><p>{{ __("student_dashboard.loading_conversation") }}</p></div>';

        // Build the correct URL with locale prefix
        const baseUrl = '{{ url(app()->getLocale() . "/student-dashboard/support/tickets") }}';
        const responsesUrl = `${baseUrl}/${ticketId}/responses`;

        // Requête AJAX pour charger les vraies réponses
        fetch(responsesUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResponses(data.responses, data.ticket);
            } else {
                conversationDiv.innerHTML = '<div class="text-center text-muted"><p>{{ __("student_dashboard.error_loading_conversation") }}</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            conversationDiv.innerHTML = '<div class="text-center text-muted"><p>{{ __("student_dashboard.error_loading_conversation") }}</p></div>';
        });
    }

    // Fonction pour afficher les réponses
    function displayResponses(responses, ticket) {
        const conversationDiv = document.getElementById('ticket-conversation');
        let html = '';

        if (responses.length === 0) {
            html = '<div class="text-center text-muted"><p>{{ __("student_dashboard.no_responses_yet") }}</p></div>';
        } else {
            responses.forEach(response => {
                const isAdmin = response.isAdmin;
                const senderName = isAdmin ? '{{ __("student_dashboard.support_team") }}' : '{{ __("student_dashboard.student") }}';
                const senderClass = isAdmin ? 'text-primary' : 'text-success';
                const timeAgo = formatTimeAgo(response.created_at);

                html += `
                    <div class="mb-3 p-3 border rounded ${isAdmin ? 'bg-light' : 'bg-primary bg-opacity-10'}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="${senderClass}">${senderName}</strong>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                        <p class="mb-0">${response.message}</p>
                    </div>
                `;
            });
        }

        conversationDiv.innerHTML = html;
        conversationDiv.scrollTop = conversationDiv.scrollHeight;
    }

    // Fonction pour formater le temps écoulé
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return '{{ __("student_dashboard.just_now") }}';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return minutes + ' {{ __("student_dashboard.minutes_ago") }}';
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return hours + ' {{ __("student_dashboard.hours_ago") }}';
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return days + ' {{ __("student_dashboard.days_ago") }}';
        }
    }
});
</script>
@endsection
