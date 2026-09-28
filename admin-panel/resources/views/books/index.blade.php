@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Books Management</h1>
        <div class="page-actions">
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                New Book
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="filter" class="me-2"></i>
                        Filters
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('books.index') }}" class="row">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Arabic title, English title or description...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Min price</label>
                            <input type="number" name="prix_min" class="form-control" 
                                   value="{{ request('prix_min') }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Max price</label>
                            <input type="number" name="prix_max" class="form-control" 
                                   value="{{ request('prix_max') }}" min="0" step="0.01">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i data-feather="search" class="me-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                                    <i data-feather="refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="book" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Book List</h4>
                            <p class="text-muted mb-0">Manage all books in the application</p>
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

                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                                                         <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>Image</th>
                                      <th>Title (Arabic)</th>
                                      <th>Title (English)</th>
                                      <th>Short Description</th>
                                      <th>Price</th>
                                      <th>File</th>
                                      <th>Summary</th>
                                      <th>Actions</th>
                                 </tr>
                             </thead>
                            <tbody>
                                                                 @forelse($books as $book)
                                     <tr>
                                         <td>{{ $book->id }}</td>
                                         <td>
                                             <img src="{{ $book->image_url }}" alt="{{ $book->titre_ar }}" 
                                                  class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                         </td>
                                         <td>
                                             <strong>{{ $book->titre_ar }}</strong>
                                         </td>
                                        <td>
                                            <strong>{{ $book->titre_en }}</strong>
                                        </td>
                                        <td>
                                            @if($book->description_short_ar)
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $book->description_short_ar }}">
                                                    {{ $book->description_short_ar }}
                                                </div>
                                            @else
                                                <span class="text-muted">No description</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $book->prix_formatted }}</span>
                                        </td>
                                                                                 <td>
                                             @if($book->file)
                                                 <a href="{{ $book->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                     <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                                     Download
                                                 </a>
                                             @else
                                                 <span class="text-muted">No file</span>
                                             @endif
                                         </td>
                                         <td>
                                             @if($book->summary)
                                                 <a href="{{ $book->summary_url }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                                     <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                                                     Summary
                                                 </a>
                                             @else
                                                 <span class="text-muted">No summary</span>
                                             @endif
                                         </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('books.show', $book) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <a href="{{ route('books.edit', $book) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <form action="{{ route('books.destroy', $book) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete" 
                                                            onclick="return confirm('Are you sure you want to delete this book?')">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                                                 @empty
                                     <tr>
                                         <td colspan="9" class="text-center">No books found</td>
                                     </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $books->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
