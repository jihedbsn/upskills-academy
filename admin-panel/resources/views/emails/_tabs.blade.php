<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('emails.index') ? 'active' : '' }}" href="{{ route('emails.index') }}">
            <i class="fa fa-paper-plane me-1"></i> Send Email
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('emails.inbox.*') ? 'active' : '' }}" href="{{ route('emails.inbox.index') }}">
            <i class="fa fa-inbox me-1"></i> Inbox
            @php
                $inboxUnread = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('email_threads')) {
                    $inboxUnread = \App\Models\EmailThread::where('unread_count', '>', 0)->sum('unread_count');
                }
            @endphp
            @if($inboxUnread > 0)
                <span class="badge bg-danger ms-1">{{ $inboxUnread }}</span>
            @endif
        </a>
    </li>
</ul>
