<style>
.sidebar-notification-badge {
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: #dc3545;
    color: white;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    display: none;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

.sidebar-notification-badge.show {
    display: flex;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>

<script>
// Sidebar notification management
document.addEventListener('DOMContentLoaded', function() {
    const apiBaseUrl = '{{ url('/api/notifications') }}';

    // Notification type to route mapping
    const notificationRoutes = {
        'purchase': '{{ route('admin.orders.index') }}',
        'installment': '{{ route('admin.order-specifiques.index') }}',
        'comment': '{{ route('admin.discussions.index') }}',
        'rating': '{{ route('admin.course-ratings.index') }}',
        'ticket': '{{ route('admin.discussions.index') }}',
        'evaluation': '{{ route('admin.resultat-quizzes.index') }}',
        'forum_comment': '{{ route('admin.discussions.index') }}',
        'student_success': '{{ route('student-successes.by-product') }}',
        'referral': '{{ route('admin.referrals.index') }}'
    };

    // Load notification counts
    function loadNotificationCounts() {
        fetch(apiBaseUrl + '/counts-by-type', {
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
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateSidebarBadges(data.counts);
            }
        })
        .catch(error => {});
    }

    // Update sidebar badges
    function updateSidebarBadges(counts) {
        // Update each notification type badge
        Object.keys(counts).forEach(type => {
            const badge = document.querySelector(`[data-notification-type="${type}"]`);
            if (badge) {
                const count = counts[type];
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.add('show');
                } else {
                    badge.classList.remove('show');
                }
            }
        });

        // Hide badges for types with no notifications
        document.querySelectorAll('[data-notification-type]').forEach(badge => {
            const type = badge.getAttribute('data-notification-type');
            if (!counts[type] || counts[type] === 0) {
                badge.classList.remove('show');
            }
        });
    }

    // Mark notifications as read when clicking sidebar item
    function markAsReadByType(type) {
        fetch(apiBaseUrl + '/mark-as-read-by-type/' + type, {
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
                loadNotificationCounts();
            }
        })
        .catch(error => {});
    }

    // Attach click handlers to sidebar items
    document.querySelectorAll('[data-notification-type]').forEach(badge => {
        const type = badge.getAttribute('data-notification-type');
        const link = badge.closest('a');

        if (link) {
            link.addEventListener('click', function(e) {
                const badgeCount = badge.textContent;
                if (badgeCount && parseInt(badgeCount) > 0) {
                    markAsReadByType(type);
                }
            });
        }
    });

    // Initial load
    loadNotificationCounts();

    // Refresh every 10 seconds
    setInterval(loadNotificationCounts, 10000);
});
</script>
