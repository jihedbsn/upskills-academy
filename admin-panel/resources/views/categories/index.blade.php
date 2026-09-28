@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Category Management</h1>
        <div class="page-actions">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                New Category
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="folder" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Category List</h4>
                            <p class="text-white-50 mb-0">Manage all product categories</p>
                        </div>
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

                    @if($categories->count() > 0)
                        <div class="mb-3">
                            <button type="button" id="saveOrderBtn" class="btn btn-success" style="display: none;">
                                <i data-feather="save" class="me-2"></i>
                                Save Order
                            </button>
                            <span class="text-muted ms-3">
                                <i data-feather="move" class="me-1"></i>
                                Drag rows to reorder
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;"></th>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Created at</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTableBody">
                                    @foreach($categories as $category)
                                        <tr data-id="{{ $category->id }}" style="cursor: move;">
                                            <td>
                                                <i data-feather="menu" class="text-muted drag-handle" style="width: 18px; height: 18px;"></i>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ $category->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="folder" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark">{{ $category->titre }}</div>
                                                        <small class="text-muted">Product category</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ optional($category->created_at)->format('Y-m-d H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="actions-column">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-info" title="View details">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="folder" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No categories found</h5>
                            <p class="text-muted">Start by creating your first category</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Create a category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('categoriesTableBody');
    const saveOrderBtn = document.getElementById('saveOrderBtn');

    if (tableBody) {
        // Initialize Sortable
        const sortable = new Sortable(tableBody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                saveOrderBtn.style.display = 'inline-block';
            }
        });

        // Save order button click handler
        saveOrderBtn.addEventListener('click', function() {
            const rows = tableBody.querySelectorAll('tr');
            const categories = [];

            rows.forEach((row, index) => {
                categories.push({
                    id: row.dataset.id,
                    order: index
                });
            });

            // Send AJAX request to save order
            fetch('{{ route("categories.update-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ categories: categories })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success modern-alert';
                    alertDiv.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                            <span>Category order updated successfully!</span>
                        </div>
                    `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

                    // Re-initialize feather icons
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }

                    // Hide save button
                    saveOrderBtn.style.display = 'none';

                    // Remove alert after 3 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                } else {
                    alert('Error updating order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order. Please try again.');
            });
        });
    }

    // Re-initialize feather icons after page load
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<style>
.sortable-ghost {
    opacity: 0.4;
    background: #f8f9fa;
}

.drag-handle {
    cursor: move;
}

tr[data-id]:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
