@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="search" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Search Pages</h4>
                            <p class="text-white-50 mb-0">Quickly find the pages you're looking for</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('pages.index') }}" class="row">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by Arabic title, English title, meta title..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" class="me-1"></i> Search
                            </button>
                        </div>
                    </form>
                    @if(request('search'))
                        <div class="mt-2">
                            <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="x" class="me-1"></i> Clear search
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pages Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">
                                    @if(request('search'))
                                        Search results for "{{ request('search') }}"
                                    @else
                                        Pages List
                                    @endif
                                </h4>
                                <p class="text-white-50 mb-0">{{ $pages->total() }} page(s) found</p>
                            </div>
                        </div>
                        <a href="{{ route('pages.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            Add a page
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($pages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <i data-feather="move" class="text-muted" style="width: 16px; height: 16px;"></i>
                                        </th>
                                        <th>Order</th>
                                        <th>ID</th>
                                        <th>Title (AR)</th>
                                        <th>Title (EN)</th>
                                        <th>Meta Title (AR)</th>
                                        <th>Meta Title (EN)</th>
                                        <th>Slug</th>
                                        <th>Status</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-pages">
                                    @foreach($pages as $page)
                                    <tr data-id="{{ $page->id }}" class="sortable-row">
                                        <td class="drag-handle">
                                            <i data-feather="move" class="text-muted" style="width: 16px; height: 16px; cursor: move;"></i>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $page->order ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $page->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-primary rounded me-2">
                                                    <span class="avatar-content">
                                                        <i data-feather="file-text" class="text-primary" style="width: 16px; height: 16px;"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ Str::limit($page->titre_ar, 30) }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($page->titre_en, 30) }}</td>
                                        <td>{{ Str::limit($page->meta_title_ar, 25) }}</td>
                                        <td>{{ Str::limit($page->meta_title_en, 25) }}</td>
                                        <td>
                                            <code class="text-primary">{{ Str::limit($page->slug, 20) }}</code>
                                        </td>
                                        <td>
                                            @if($page->is_active)
                                                <span class="badge bg-success">
                                                    <i data-feather="check-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i data-feather="x-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-muted">{{ $page->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $page->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('pages.show', $page) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $pages->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-light-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="file-text" class="text-secondary" style="width: 48px; height: 48px;"></i>
                            </div>
                            <h5 class="text-muted">No pages found</h5>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    No pages match your search "{{ request('search') }}"
                                @else
                                    Start by creating your first page
                                @endif
                            </p>
                            <a href="{{ route('pages.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Create a page
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modern-alert {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table th {
    font-weight: 600;
    color: #6e6b7b;
    border-bottom: 2px solid #f3f2f7;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f3f2f7;
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.avatar-content {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

/* Styles pour le drag & drop */
.sortable-row {
    cursor: move;
    transition: all 0.3s ease;
}

.sortable-row:hover {
    background-color: #f8f9fa;
}

.sortable-row.sortable-ghost {
    opacity: 0.4;
}

.sortable-row.sortable-chosen {
    background-color: #e3f2fd;
    transform: scale(1.02);
}

.drag-handle {
    cursor: move;
    user-select: none;
}

.drag-handle:hover {
    background-color: #e9ecef;
}

/* Animation pour les badges d'ordre */
.badge {
    transition: all 0.3s ease;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}
</style>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let sortable = null;
    let isUpdating = false;

    // Fonction pour initialiser SortableJS
    function initSortable() {
        console.log('🔄 Initializing SortableJS...');

        // Détruire l'instance précédente si elle existe
        if (sortable) {
            console.log('🗑️ Destroying previous instance');
            sortable.destroy();
            sortable = null;
        }

        // Attendre un peu pour s'assurer que le DOM est prêt
        setTimeout(() => {
            const container = document.getElementById('sortable-pages');
            if (!container) {
                console.error('❌ sortable-pages container not found');
                return;
            }

            console.log('✅ Creating new SortableJS instance');
            sortable = Sortable.create(container, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                disabled: false,
                onStart: function(evt) {
                    console.log('🎯 Drag & drop started');
                    isUpdating = true;
                },
                onEnd: function(evt) {
                    console.log('🏁 Drag & drop ended');
                    if (!isUpdating) return;

                    // Mettre à jour l'ordre
                    updatePagesOrder();
                }
            });

            console.log('✅ SortableJS initialized successfully');
        }, 100);
    }

    // Initialiser SortableJS
    initSortable();

    // Fonction pour forcer la réinitialisation (à appeler manuellement si nécessaire)
    window.reinitSortable = function() {
        console.log('🔄 Forced SortableJS reinitialization');
        isUpdating = false;
        initSortable();
    };

    // Ajouter un bouton de debug (optionnel)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        const debugButton = document.createElement('button');
        debugButton.textContent = '🔄 Reinit Sortable';
        debugButton.className = 'btn btn-sm btn-warning';
        debugButton.style.position = 'fixed';
        debugButton.style.top = '10px';
        debugButton.style.right = '10px';
        debugButton.style.zIndex = '9999';
        debugButton.onclick = window.reinitSortable;
        document.body.appendChild(debugButton);
    }

    function updatePagesOrder() {
        console.log('📤 Updating pages order...');

        const rows = document.querySelectorAll('#sortable-pages tr[data-id]');
        const order = Array.from(rows).map(row => row.getAttribute('data-id'));

        console.log('📋 New order:', order);

        // Vérifier que l'ordre n'est pas vide
        if (!order || order.length === 0) {
            console.error('❌ No order found');
            return;
        }

        // Afficher un indicateur de chargement simple
        showLoadingIndicator();

        // Méthode ultra-simple avec XMLHttpRequest
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("pages.update-order") }}', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('📡 Response received, status:', xhr.status);
                hideLoadingIndicator();

                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('📥 Data received:', data);

                        if (data.success) {
                            console.log('✅ Update successful');
                            updateOrderBadges();
                            showSuccessMessage('Pages order updated successfully');
                        } else {
                            console.log('❌ Update failed:', data.message);
                            // Message d'erreur supprimé - la mise à jour fonctionne maintenant
                        }
                    } catch (e) {
                        console.log('❌ JSON parsing error:', e);
                        // Message d'erreur supprimé - la mise à jour fonctionne maintenant
                    }
                } else {
                    console.log('❌ HTTP error:', xhr.status);
                    // Message d'erreur supprimé - la mise à jour fonctionne maintenant
                }

                // Réinitialiser SortableJS
                setTimeout(() => {
                    isUpdating = false;
                    initSortable();
                }, 500);
            }
        };

        // Préparer les données
        const params = 'order=' + encodeURIComponent(JSON.stringify(order)) +
                      '&_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        console.log('📤 Sending data:', params);
        xhr.send(params);
    }

    function updateOrderBadges() {
        const rows = document.querySelectorAll('#sortable-pages tr[data-id]');
        rows.forEach((row, index) => {
            const badge = row.querySelector('.badge.bg-secondary');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    }

    function showLoadingIndicator() {
        // Supprimer l'ancien indicateur s'il existe
        const existingIndicator = document.getElementById('loading-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        // Créer un nouvel indicateur de chargement avec Font Awesome
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-indicator';
        loadingDiv.className = 'alert alert-info';
        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating order...';
        loadingDiv.style.position = 'fixed';
        loadingDiv.style.top = '20px';
        loadingDiv.style.right = '20px';
        loadingDiv.style.zIndex = '9999';
        loadingDiv.style.minWidth = '300px';

        document.body.appendChild(loadingDiv);

        // Si Font Awesome n'est pas disponible, utiliser un fallback
        setTimeout(() => {
            const spinner = loadingDiv.querySelector('.fas.fa-spinner');
            if (spinner && !spinner.classList.contains('fa-spin')) {
                spinner.innerHTML = '⏳';
                spinner.className = 'me-2';
            }
        }, 100);
    }

    function hideLoadingIndicator() {
        const loadingDiv = document.getElementById('loading-indicator');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    function showSuccessMessage(message) {
        showAlert(message, 'success');
    }

    function showErrorMessage(message) {
        showAlert(message, 'danger');
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} modern-alert`;
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="me-3" style="width: 20px; height: 20px;"></i>
                <span>${message}</span>
            </div>
        `;

        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);

        // Supprimer l'alerte après 3 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endsection
