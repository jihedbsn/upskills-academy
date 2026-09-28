<li class="nav-item notification-bell-container" style="position: relative;">
    <a href="#" id="notification-bell" class="nav-link position-relative" style="font-size: 1.3rem;">
        <i class="bi bi-bell"></i>
        <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.65rem; padding: 0.25em 0.5em;">
            0
        </span>
    </a>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown" class="dropdown-menu dropdown-menu-end shadow-lg" style="display: none; position: absolute; right: 0; min-width: 380px; max-width: 450px; max-height: 500px; overflow-y: auto; z-index: 9999; top: 100%; margin-top: 0.5rem;">
        <div class="dropdown-header d-flex justify-content-between align-items-center bg-light py-3 px-3">
            <h6 class="mb-0 fw-bold">Notifications</h6>
            <button id="mark-all-read" class="btn btn-sm btn-link text-primary p-0" style="font-size: 0.85rem;">
                Mark all as read
            </button>
        </div>

        <div id="notification-list" class="list-group list-group-flush">
            <!-- Notifications will be loaded here -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <div class="dropdown-footer bg-light py-2 px-3 text-center border-top">
            <a href="{{ url('/notifications') }}" class="text-decoration-none text-primary fw-semibold" style="font-size: 0.9rem;">
                View all notifications
            </a>
        </div>
    </div>
</li>

<style>
.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e7f3ff;
    border-left: 3px solid #0d6efd;
}

.notification-item.important {
    background-color: #fff3cd;
    border-left: 3px solid #ffc107;
}

.notification-icon {
    font-size: 1.5rem;
    margin-right: 12px;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 4px;
    color: #333;
}

.notification-message {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 0.75rem;
    color: #999;
}

.notification-actions {
    display: none;
}

.notification-item:hover .notification-actions {
    display: flex;
    gap: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bellButton = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');

    const apiBaseUrl = '{{ url('/api/notifications') }}';
    let isDropdownOpen = false;

    // Toggle dropdown
    bellButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;
        dropdown.style.display = isDropdownOpen ? 'block' : 'none';

        if (isDropdownOpen) {
            loadNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !bellButton.contains(e.target)) {
            isDropdownOpen = false;
            dropdown.style.display = 'none';
        }
    });

    // Load unread count
    function loadUnreadCount() {
        fetch(apiBaseUrl + '/unread-count', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.error('HTTP Error:', response.status, response.statusText);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading unread count:', error);
            // Hide badge on error
            badge.style.display = 'none';
        });
    }

    // Load notifications
    function loadNotifications() {
        notificationList.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        fetch(apiBaseUrl + '?per_page=10', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications.data.length > 0) {
                notificationList.innerHTML = data.notifications.data.map(notification => createNotificationItem(notification)).join('');
            } else {
                notificationList.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No notifications</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Error loading notifications</p>
                </div>
            `;
        });
    }

    // Create notification item HTML
    function createNotificationItem(notification) {
        const isUnread = !notification.read_at;
        const isImportant = notification.is_important;
        const classes = ['notification-item', 'd-flex', 'align-items-start'];

        if (isUnread) classes.push('unread');
        if (isImportant) classes.push('important');

        return `
            <div class="${classes.join(' ')}" data-id="${notification.id}" data-url="${notification.action_url || '#'}">
                <div class="notification-icon">${notification.icon || '📬'}</div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${new Date(notification.created_at).toLocaleString()}</div>
                </div>
                ${isUnread ? `<button class="btn btn-sm btn-link text-primary mark-read-btn" data-id="${notification.id}"><i class="bi bi-check2"></i></button>` : ''}
            </div>
        `;
    }

    // Mark notification as read
    notificationList.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        const markReadBtn = e.target.closest('.mark-read-btn');

        if (markReadBtn) {
            e.stopPropagation();
            const notificationId = markReadBtn.dataset.id;
            markAsRead(notificationId);
        } else if (notificationItem) {
            const notificationId = notificationItem.dataset.id;
            // Just mark as read, don't navigate
            markAsRead(notificationId);
        }
    });

    // Mark as read function
    function markAsRead(notificationId, callback) {
        fetch(apiBaseUrl + '/' + notificationId + '/mark-as-read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadUnreadCount();
                loadNotifications();
                if (callback) callback();
            }
        })
        .catch(error => console.error('Error marking as read:', error));
    }

    // Mark all as read
    markAllReadBtn.addEventListener('click', function(e) {
        e.stopPropagation();

        fetch(apiBaseUrl + '/mark-all-as-read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadUnreadCount();
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    });

    // Load unread count on page load
    loadUnreadCount();

    // Refresh unread count every 10 seconds
    setInterval(loadUnreadCount, 10000);
});
</script>
