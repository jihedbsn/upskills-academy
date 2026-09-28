@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Course Ratings Management</h4>
                <div class="card-actions">
                    <button class="btn btn成功" onclick="bulkApprove()">
                        <i data-feather="check"></i> Approve Selected
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="status_filter" class="form-label">Status</label>
                        <select class="form-select" id="status_filter">
                            <option value="">All</option>
                            <option value="1">Approved</option>
                            <option value="0">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="product_filter" class="form-label">Course</label>
                        <select class="form-select" id="product_filter">
                            <option value="">All</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="rating_filter" class="form-label">Rating</label>
                        <select class="form-select" id="rating_filter">
                            <option value="">All</option>
                            <option value="5">5 stars</option>
                            <option value="4">4 stars</option>
                            <option value="3">3 stars</option>
                            <option value="2">2 stars</option>
                            <option value="1">1 star</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="student_filter" class="form-label">Student</label>
                        <input type="text" class="form-control" id="student_filter" placeholder="Nom ou email">
                    </div>
                    <div class="col-md-2">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary d-block" onclick="applyFilters()">
                            <i data-feather="filter"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Ratings table -->
                <div class="table-responsive">
                    <table class="table table-striped" id="ratings-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                </th>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ratings as $rating)
                            <tr>
                                <td>
                                    <input type="checkbox" class="rating-checkbox" value="{{ $rating->id }}">
                                </td>
                                <td>{{ $rating->id }}</td>
                                <td>
                                    @if($rating->student)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content">{{ $rating->student->initials }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $rating->student->full_name }}</h6>
                                            <small class="text-muted">{{ $rating->student->email }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-content bg-secondary">?</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-muted">Student Deleted</h6>
                                            <small class="text-muted">N/A</small>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $rating->product ? ($rating->product->titre ?? $rating->product->name_en ?? $rating->product->name_ar ?? 'N/A') : 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i data-feather="star" class="text-warning" style="width: 16px; height: 16px; {{ $i <= $rating->rating ? 'fill: #ffc107;' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="fw-bold">{{ $rating->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    @if($rating->commentaire)
                                        <div class="comment-preview">
                                            <div class="text-truncate" style="max-width: 200px;">
                                                {{ Str::limit($rating->commentaire, 50) }}
                                            </div>
                                            @if(strlen($rating->commentaire) > 50)
                                                <a href="#" class="text-primary small" onclick="showCommentModal({{ $rating->id }}, event)">
                                                    Read more...
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">No comment</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rating->is_approved)
                                        <span class="badge bg-success">Approuvé</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </td>
                                <td>{{ $rating->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i data-feather="more-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="showCommentModal({{ $rating->id }}, event)">
                                                <i data-feather="message-square" class="me-2"></i>View Comment
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="showRespondModal({{ $rating->id }}, event)">
                                                <i data-feather="message-circle" class="me-2"></i>Respond
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.course-ratings.show', $rating) }}">
                                                <i data-feather="eye" class="me-2"></i>View Details
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.course-ratings.edit', $rating) }}">
                                                <i data-feather="edit" class="me-2"></i>Edit
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($rating->is_approved)
                                                <li><a class="dropdown-item text-warning" href="#" onclick="toggleApproval({{ $rating->id }}, false)">
                                                    <i data-feather="x-circle" class="me-2"></i>Disapprove
                                                </a></li>
                                            @else
                                                <li><a class="dropdown-item text-success" href="#" onclick="toggleApproval({{ $rating->id }}, true)">
                                                    <i data-feather="check-circle" class="me-2"></i>Approve
                                                </a></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteRating({{ $rating->id }})">
                                                <i data-feather="trash-2" class="me-2"></i>Delete
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i data-feather="star" class="mb-2" style="width: 48px; height: 48px;"></i>
                                        <p>No ratings found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <div class="text-muted">
                        Showing {{ $ratings->firstItem() ?? 0 }} to {{ $ratings->lastItem() ?? 0 }} of {{ $ratings->total() }} ratings
                    </div>
                    <div>
                        {{ $ratings->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-3">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <small>Total Ratings</small>
                    </div>
                    <i data-feather="star" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                        <small>Approved</small>
                    </div>
                    <i data-feather="check-circle" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        <small>Pending</small>
                    </div>
                    <i data-feather="clock" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['average_rating'], 1) }}/5</h4>
                        <small>Average Rating</small>
                    </div>
                    <i data-feather="bar-chart-2" style="width: 48px; height: 48px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rating distribution -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Rating Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $stats['rating_distribution'][$star] ?? 0;
                            $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                        @endphp
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <div class="d-flex justify-content-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-feather="star" class="text-warning" style="width: 16px; height: 16px; {{ $i <= $star ? 'fill: #ffc107;' : '' }}"></i>
                                    @endfor
                                </div>
                                <h6 class="mb-1">{{ $count }}</h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rating Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalCommentContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Respond Modal -->
<div class="modal fade" id="respondModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i data-feather="message-circle" class="me-2"></i>Respond to Rating</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="respondModalInfo" class="mb-4">
                    <!-- Rating info will be loaded here -->
                </div>
                <hr>
                <div class="mb-3">
                    <label for="adminResponseText" class="form-label fw-bold">Your Response:</label>
                    <textarea class="form-control" id="adminResponseText" rows="5" placeholder="Enter your response to this rating..."></textarea>
                    <div class="form-text">This response will be visible to the student.</div>
                </div>
                <div id="existingResponse" class="d-none">
                    <div class="alert alert-info">
                        <strong>Previous Response:</strong>
                        <div id="existingResponseText" class="mt-2"></div>
                        <small id="existingResponseDate" class="text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitResponseBtn" onclick="submitResponse()">
                    <i data-feather="send" class="me-2" style="width: 16px; height: 16px;"></i>Send Response
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.comment-preview {
    max-width: 250px;
}
.comment-preview .text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<script>
function applyFilters() {
    const status = document.getElementById('status_filter').value;
    const product = document.getElementById('product_filter').value;
    const rating = document.getElementById('rating_filter').value;
    const student = document.getElementById('student_filter').value;
    const date = document.getElementById('date_filter').value;

    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (product) url.searchParams.set('product', product);
    if (rating) url.searchParams.set('rating', rating);
    if (student) url.searchParams.set('student', student);
    if (date) url.searchParams.set('date', date);

    window.location.href = url.toString();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.rating-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkApprove() {
    const selectedIds = Array.from(document.querySelectorAll('.rating-checkbox:checked'))
                           .map(checkbox => checkbox.value);

    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins une évaluation');
        return;
    }

    if (!confirm(`Êtes-vous sûr de vouloir approuver ${selectedIds.length} évaluation(s) ?`)) {
        return;
    }

    fetch('/admin/course-ratings/bulk-approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ rating_ids: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de l\'approbation');
        }
    });
}

function toggleApproval(ratingId, approved) {
    const action = approved ? 'approuver' : 'désapprouver';
    if (!confirm(`Êtes-vous sûr de vouloir ${action} cette évaluation ?`)) {
        return;
    }

    const url = approved ?
        `/admin/course-ratings/${ratingId}/approve` :
        `/admin/course-ratings/${ratingId}/disapprove`;

    fetch(url, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    });
}

function deleteRating(ratingId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')) {
        return;
    }

    fetch(`/admin/course-ratings/${ratingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la suppression');
        }
    });
}

// Store ratings data for modal
const ratingsData = {!! json_encode($ratings->map(function($rating) {
    return [
        'id' => $rating->id,
        'rating' => $rating->rating,
        'commentaire' => $rating->commentaire,
        'admin_response' => $rating->admin_response,
        'admin_response_at' => $rating->admin_response_at ? $rating->admin_response_at->format('d/m/Y H:i') : null,
        'is_approved' => $rating->is_approved,
        'created_at' => $rating->created_at,
        'student' => $rating->student ? [
            'first_name' => $rating->student->first_name,
            'last_name' => $rating->student->last_name,
            'email' => $rating->student->email,
        ] : null,
        'product' => $rating->product ? [
            'id' => $rating->product->id,
            'titre' => $rating->product->titre ?? null,
            'name_en' => $rating->product->name_en ?? null,
            'name_ar' => $rating->product->name_ar ?? null,
        ] : null,
    ];
})->values()) !!};

let currentRespondRatingId = null;

function showCommentModal(ratingId, event) {
    event.preventDefault();

    const rating = ratingsData.find(r => r.id === ratingId);
    if (!rating) {
        console.error('Rating not found:', ratingId);
        return;
    }

    console.log('Rating data:', rating);
    console.log('Product data:', rating.product);

    const modalContent = document.getElementById('modalCommentContent');
    const studentName = rating.student ? `${rating.student.first_name} ${rating.student.last_name}` : 'Unknown';

    // Try multiple field names for product title
    let productTitle = 'Unknown Course';
    if (rating.product) {
        productTitle = rating.product.titre || rating.product.name_en || rating.product.name_ar || rating.product.title || `Course #${rating.product.id}`;
    }

    console.log('Product title resolved:', productTitle);

    // Generate stars
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<i data-feather="star" class="text-warning" style="width: 20px; height: 20px; ${i <= rating.rating ? 'fill: #ffc107;' : ''}"></i>`;
    }

    modalContent.innerHTML = `
        <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
                <strong class="me-2">Student:</strong> ${studentName}
            </div>
            <div class="d-flex align-items-center mb-2">
                <strong class="me-2">Course:</strong> <span class="badge bg-primary">${productTitle}</span>
            </div>
            <div class="d-flex align-items-center mb-2">
                <strong class="me-2">Rating:</strong>
                <div class="d-flex me-2">${starsHtml}</div>
                <span class="fw-bold">${rating.rating}/5</span>
            </div>
            <div class="d-flex align-items-center mb-2">
                <strong class="me-2">Date:</strong> ${new Date(rating.created_at).toLocaleDateString('fr-FR')}
            </div>
            <div class="d-flex align-items-center mb-3">
                <strong class="me-2">Status:</strong>
                ${rating.is_approved ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning">Pending</span>'}
            </div>
        </div>
        <hr>
        <div class="mt-3">
            <strong class="d-block mb-2">Comment:</strong>
            <div class="p-3 bg-light rounded">
                ${rating.commentaire || '<em class="text-muted">No comment provided</em>'}
            </div>
        </div>
    `;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('commentModal'));
    modal.show();

    // Re-initialize feather icons for the modal
    setTimeout(() => feather.replace(), 100);
}

function showRespondModal(ratingId, event) {
    event.preventDefault();

    const rating = ratingsData.find(r => r.id === ratingId);
    if (!rating) {
        console.error('Rating not found:', ratingId);
        return;
    }

    currentRespondRatingId = ratingId;

    const modalInfo = document.getElementById('respondModalInfo');
    const studentName = rating.student ? `${rating.student.first_name} ${rating.student.last_name}` : 'Unknown';

    let productTitle = 'Unknown Course';
    if (rating.product) {
        productTitle = rating.product.titre || rating.product.name_en || rating.product.name_ar || rating.product.title || `Course #${rating.product.id}`;
    }

    // Generate stars
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<i data-feather="star" class="text-warning" style="width: 18px; height: 18px; ${i <= rating.rating ? 'fill: #ffc107;' : ''}"></i>`;
    }

    modalInfo.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <strong class="me-2">Student:</strong> ${studentName}
                </div>
                <div class="d-flex align-items-center mb-2">
                    <strong class="me-2">Course:</strong> <span class="badge bg-primary">${productTitle}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <strong class="me-2">Rating:</strong>
                    <div class="d-flex me-2">${starsHtml}</div>
                    <span class="fw-bold">${rating.rating}/5</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <strong class="me-2">Date:</strong> ${new Date(rating.created_at).toLocaleDateString('fr-FR')}
                </div>
            </div>
        </div>
        <div class="mt-3 p-3 bg-light rounded">
            <strong class="d-block mb-2">Student's Comment:</strong>
            ${rating.commentaire || '<em class="text-muted">No comment provided</em>'}
        </div>
    `;

    // Show existing response if any
    const existingResponseDiv = document.getElementById('existingResponse');
    const existingResponseText = document.getElementById('existingResponseText');
    const existingResponseDate = document.getElementById('existingResponseDate');
    const adminResponseTextarea = document.getElementById('adminResponseText');

    if (rating.admin_response) {
        existingResponseDiv.classList.remove('d-none');
        existingResponseText.textContent = rating.admin_response;
        existingResponseDate.textContent = `Responded on: ${rating.admin_response_at}`;
        adminResponseTextarea.value = rating.admin_response;
    } else {
        existingResponseDiv.classList.add('d-none');
        adminResponseTextarea.value = '';
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('respondModal'));
    modal.show();

    // Re-initialize feather icons for the modal
    setTimeout(() => feather.replace(), 100);
}

function submitResponse() {
    if (!currentRespondRatingId) {
        alert('Error: No rating selected');
        return;
    }

    const responseText = document.getElementById('adminResponseText').value.trim();
    if (!responseText) {
        alert('Please enter a response');
        return;
    }

    const submitBtn = document.getElementById('submitResponseBtn');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    fetch(`/admin/course-ratings/${currentRespondRatingId}/respond`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ admin_response: responseText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the local data
            const rating = ratingsData.find(r => r.id === currentRespondRatingId);
            if (rating) {
                rating.admin_response = data.admin_response;
                rating.admin_response_at = data.admin_response_at;
            }

            // Close modal and show success
            bootstrap.Modal.getInstance(document.getElementById('respondModal')).hide();
            alert('Response saved successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to save response'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the response');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        setTimeout(() => feather.replace(), 100);
    });
}
</script>
@endpush
