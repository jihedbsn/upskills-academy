@extends('layouts.app')

@section('title', 'Courses and Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Courses and Students
                    </h4>
                    <p class="card-subtitle text-muted">
                        List of courses with enrolled students count
                    </p>
                </div>
                <div class="card-body">
                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fa fa-graduation-cap fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $courses->count() }}</h4>
                                    <small>Active Courses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fa fa-users fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $courses->sum('students_count') }}</h4>
                                    <small>Total Students</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fa fa-line-chart fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $courses->count() > 0 ? round($courses->sum('students_count') / $courses->count(), 1) : 0 }}</h4>
                                    <small>Average per course</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fa fa-trophy fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $courses->max('students_count') }}</h4>
                                    <small>Max Students</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search a course...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="sortSelect" class="form-select">
                                <option value="students_count_desc">Most students</option>
                                <option value="students_count_asc">Fewest students</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                                <option value="date_desc">Newest</option>
                                <option value="date_asc">Oldest</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="perPageSelect" class="form-select">
                                <option value="10">10 per page</option>
                                <option value="25" selected>25 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>
                        </div>
                    </div>

                    @if(isset($courses) && $courses && $courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="coursesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Students Count</th>
                                        <th>Created at</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $index => $course)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fa fa-graduation-cap text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $course->titre ?? 'Course #' . $course->id }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $course->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $course->students_count }} student(s)
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $course->created_at->format('Y-m-d') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('course-students.show', $course->id) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-eye me-1"></i>
                                                    View students
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $courses->firstItem() ?? 0 }} to {{ $courses->lastItem() ?? 0 }} 
                                of {{ $courses->total() }} courses
                            </div>
                            <div>
                                {{ $courses->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No courses with students</h5>
                            <p class="text-muted">
                                There are currently no courses with enrolled students.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
}

.card.bg-primary, .card.bg-success, .card.bg-info, .card.bg-warning {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card.bg-primary:hover, .card.bg-success:hover, .card.bg-info:hover, .card.bg-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const perPageSelect = document.getElementById('perPageSelect');
    const table = document.getElementById('coursesTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    let currentPage = 1;
    let itemsPerPage = 25;
    let filteredRows = [...rows];
    
    // Fonction de recherche
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        filteredRows = rows.filter(row => {
            const courseName = row.querySelector('td:nth-child(2) strong').textContent.toLowerCase();
            const courseId = row.querySelector('td:nth-child(2) small').textContent.toLowerCase();
            return courseName.includes(searchTerm) || courseId.includes(searchTerm);
        });
        currentPage = 1;
        updateDisplay();
    }
    
    // Fonction de tri
    function sortRows() {
        const sortValue = sortSelect.value;
        filteredRows.sort((a, b) => {
            switch(sortValue) {
                case 'students_count_desc':
                    return parseInt(b.querySelector('td:nth-child(3) .badge').textContent) - 
                           parseInt(a.querySelector('td:nth-child(3) .badge').textContent);
                case 'students_count_asc':
                    return parseInt(a.querySelector('td:nth-child(3) .badge').textContent) - 
                           parseInt(b.querySelector('td:nth-child(3) .badge').textContent);
                case 'name_asc':
                    return a.querySelector('td:nth-child(2) strong').textContent.localeCompare(
                        b.querySelector('td:nth-child(2) strong').textContent);
                case 'name_desc':
                    return b.querySelector('td:nth-child(2) strong').textContent.localeCompare(
                        a.querySelector('td:nth-child(2) strong').textContent);
                case 'date_desc':
                    return new Date(b.querySelector('td:nth-child(4) small').textContent.split('/').reverse().join('-')) - 
                           new Date(a.querySelector('td:nth-child(4) small').textContent.split('/').reverse().join('-'));
                case 'date_asc':
                    return new Date(a.querySelector('td:nth-child(4) small').textContent.split('/').reverse().join('-')) - 
                           new Date(b.querySelector('td:nth-child(4) small').textContent.split('/').reverse().join('-'));
                default:
                    return 0;
            }
        });
        updateDisplay();
    }
    
    // Fonction de pagination
    function updateDisplay() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageRows = filteredRows.slice(startIndex, endIndex);
        
        // Vider le tbody
        tbody.innerHTML = '';
        
        // Ajouter les lignes de la page actuelle
        pageRows.forEach((row, index) => {
            const newRow = row.cloneNode(true);
            newRow.querySelector('td:first-child').textContent = startIndex + index + 1;
            tbody.appendChild(newRow);
        });
        
        // Mettre à jour les informations de pagination
        updatePaginationInfo();
    }
    
    // Fonction pour mettre à jour les informations de pagination
    function updatePaginationInfo() {
        const totalItems = filteredRows.length;
        const startItem = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        
        // Update pagination text
        const paginationInfo = document.querySelector('.d-flex.justify-content-between .text-muted');
        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} courses`;
        }
    }
    
    // Événements
    searchInput.addEventListener('input', filterRows);
    sortSelect.addEventListener('change', sortRows);
    perPageSelect.addEventListener('change', function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1;
        updateDisplay();
    });
    
    // Initialisation
    updateDisplay();
});
</script>
@endsection
