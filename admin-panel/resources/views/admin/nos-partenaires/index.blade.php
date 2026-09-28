@extends('layouts.app')

@section('title', 'Our Partners')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Partners Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('nos-partenaires.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Partner
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($partenaires->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">Order</th>
                                        <th width="100">Logo</th>
                                        <th>Name</th>
                                        <th>URL</th>
                                        <th width="100">Status</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-table">
                                    @foreach($partenaires as $partenaire)
                                        <tr data-id="{{ $partenaire->id }}">
                                            <td class="text-center">
                                                <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                                                <span class="badge badge-secondary">{{ $partenaire->order }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($partenaire->logo)
                                                    <img src="{{ $partenaire->logo_url }}" alt="{{ $partenaire->nom }}" 
                                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $partenaire->nom }}</strong>
                                            </td>
                                            <td>
                                                @if($partenaire->url)
                                                    <a href="{{ $partenaire->url }}" target="_blank" class="text-primary">
                                                        {{ Str::limit($partenaire->url, 50) }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">No URL</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($partenaire->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('nos-partenaires.show', $partenaire) }}" 
                                                       class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('nos-partenaires.edit', $partenaire) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('nos-partenaires.destroy', $partenaire) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this partner?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No partners found</h4>
                            <p class="text-muted">Start by adding your first partner.</p>
                            <a href="{{ route('nos-partenaires.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Partner
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.4;
    }
    
    .sortable-chosen {
        background-color: #f8f9fa;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Initialiser le tri par glisser-déposer
    const sortable = Sortable.create(document.getElementById('sortable-table'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function(evt) {
            // Récupérer les IDs dans le nouvel ordre
            const rows = document.querySelectorAll('#sortable-table tr');
            const partenaires = [];
            
            rows.forEach((row, index) => {
                const id = row.getAttribute('data-id');
                if (id) {
                    partenaires.push(id);
                }
            });
            
            // Envoyer la requête AJAX pour mettre à jour l'ordre
            $.ajax({
                url: '{{ route("nos-partenaires.update-order") }}',
                method: 'POST',
                data: {
                    partenaires: partenaires,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Mettre à jour les numéros d'ordre affichés
                        rows.forEach((row, index) => {
                            const badge = row.querySelector('.badge-secondary');
                            if (badge) {
                                badge.textContent = index + 1;
                            }
                        });
                        
                        // Show success message
                        toastr.success('Order updated successfully');
                    }
                },
                error: function() {
                    toastr.error('Error updating order');
                }
            });
        }
    });
});
</script>
@endpush
