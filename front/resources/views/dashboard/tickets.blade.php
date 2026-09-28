@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.support_tickets') }}</h4>
            <button class="button -md -purple-1 text-white" data-bs-toggle="modal" data-bs-target="#newTicketModal">
              {{ __('messages.create_ticket') }}
            </button>
          </div>

          @if(session('success'))
            <div class="alert alert-success mb-30">
              {{ session('success') }}
            </div>
          @endif

          @if(empty($tickets))
            <div class="text-center py-50">
              <i class="text-60 icon-message text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_tickets_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.need_help_message') }}</p>
              <button class="button -md -orange-1 text-white mt-20" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                {{ __('messages.create_ticket') }}
              </button>
            </div>
          @else
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ __('messages.ticket_id') }}</th>
                    <th>{{ __('messages.subject') }}</th>
                    <th>{{ __('messages.priority') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tickets as $ticket)
                    <tr>
                      <td>#{{ $ticket->id }}</td>
                      <td>{{ $ticket->subject }}</td>
                      <td>
                        <span class="badge {{ $ticket->priority == 'high' ? 'bg-red-1' : ($ticket->priority == 'medium' ? 'bg-orange-1' : 'bg-green-1') }}">
                          {{ $ticket->priority }}
                        </span>
                      </td>
                      <td>
                        <span class="badge {{ $ticket->status == 'open' ? 'bg-blue-1' : 'bg-green-1' }}">
                          {{ $ticket->status }}
                        </span>
                      </td>
                      <td>{{ $ticket->created_at }}</td>
                      <td>
                        <a href="#" class="button -sm -purple-1 text-white">{{ __('messages.view') }}</a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newTicketModalLabel">{{ __('messages.create_new_ticket') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('dashboard.tickets.create') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="subject" class="form-label">{{ __('messages.subject') }} *</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
          </div>
          <div class="mb-3">
            <label for="priority" class="form-label">{{ __('messages.priority') }} *</label>
            <select class="form-select" id="priority" name="priority" required>
              <option value="low">{{ __('messages.low') }}</option>
              <option value="medium">{{ __('messages.medium') }}</option>
              <option value="high">{{ __('messages.high') }}</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">{{ __('messages.message') }} *</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button -md -light-1" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
          <button type="submit" class="button -md -purple-1 text-white">{{ __('messages.create_ticket') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
