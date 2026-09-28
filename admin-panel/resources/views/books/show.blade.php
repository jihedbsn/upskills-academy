@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Book Details</h1>
        <div class="page-actions">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i data-feather="more-horizontal" class="me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('books.edit', $book) }}">
                            <i data-feather="edit" class="me-1"></i>
                            Edit
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('books.index') }}">
                            <i data-feather="list" class="me-1"></i>
                            Back to list
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

         <!-- Book header -->
     <div class="card">
         <div class="card-header">
             <div class="d-flex align-items-center">
                 <div class="me-3">
                     <img src="{{ $book->image_url }}" alt="{{ $book->titre_ar }}" 
                          class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                 </div>
                 <div class="flex-grow-1">
                     <h4 class="card-title mb-1">{{ $book->titre_ar }}</h4>
                     <p class="text-muted mb-0">{{ $book->titre_en }}</p>
                 </div>
                 <div class="text-end">
                     <div class="badge rounded-pill bg-success fs-6">
                         <i data-feather="dollar-sign" class="me-1"></i>
                         {{ $book->prix_formatted }}
                     </div>
                 </div>
             </div>
         </div>
     </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="info" class="me-2"></i>
                        Book Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Title (Arabic)</label>
                                <div class="form-control-plaintext fw-bolder">{{ $book->titre_ar }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Title (English)</label>
                                <div class="form-control-plaintext fw-bolder">{{ $book->titre_en }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Price</label>
                                <div class="form-control-plaintext">
                                    <span class="badge rounded-pill bg-success fs-6">
                                        {{ $book->prix_formatted }}
                                    </span>
                                </div>
                            </div>
                        </div>
                                                 <div class="col-md-6">
                             <div class="mb-3">
                                 <label class="form-label text-muted">File</label>
                                 <div class="form-control-plaintext">
                                     @if($book->file)
                                         <a href="{{ $book->file_url }}" target="_blank" class="btn btn-outline-info">
                                             <i data-feather="download" class="me-1"></i>
                                             Download file
                                         </a>
                                     @else
                                         <span class="text-muted">No file</span>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label class="form-label text-muted">Summary</label>
                                 <div class="form-control-plaintext">
                                     @if($book->summary)
                                         <a href="{{ $book->summary_url }}" target="_blank" class="btn btn-outline-warning">
                                             <i data-feather="file-text" class="me-1"></i>
                                             Download summary
                                         </a>
                                     @else
                                         <span class="text-muted">No summary</span>
                                     @endif
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label class="form-label text-muted">Image</label>
                                 <div class="form-control-plaintext">
                                     @if($book->image)
                                         <img src="{{ $book->image_url }}" alt="{{ $book->titre_ar }}" 
                                              class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                     @else
                                         <span class="text-muted">No image</span>
                                     @endif
                                 </div>
                             </div>
                         </div>
                    </div>

                    @if($book->description_short_ar || $book->description_short_en)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Short Description (Arabic)</label>
                                    <div class="form-control-plaintext">
                                        {{ $book->description_short_ar ?: 'Not defined' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Short Description (English)</label>
                                    <div class="form-control-plaintext">
                                        {{ $book->description_short_en ?: 'Not defined' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Full descriptions -->
            @if($book->description_ar || $book->description_en)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i data-feather="file-text" class="me-2"></i>
                            Full Descriptions
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($book->description_ar)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Description (Arabic)</label>
                                        <div class="border rounded p-3 bg-light">
                                            {!! $book->description_ar !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($book->description_en)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Description (English)</label>
                                        <div class="border rounded p-3 bg-light">
                                            {!! $book->description_en !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics and metadata -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="bar-chart-2" class="me-2"></i>
                        Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Book ID</span>
                            <span class="fw-bolder">#{{ $book->id }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Created at</span>
                            <span class="fw-bolder">{{ $book->created_at->format('d/m/Y') }}</span>
                        </div>
                        <small class="text-muted">{{ $book->created_at->format('H:i') }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Last updated</span>
                            <span class="fw-bolder">{{ $book->updated_at->format('d/m/Y') }}</span>
                        </div>
                        <small class="text-muted">{{ $book->updated_at->format('H:i') }}</small>
                    </div>

                    @if($book->file)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">File size</span>
                                <span class="fw-bolder">
                                    @php
                                        $filePath = public_path('uploads/books/' . $book->file);
                                        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                        $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024 / 1024, 2) . ' MB' : 'N/A';
                                    @endphp
                                    {{ $fileSizeFormatted }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="zap" class="me-2"></i>
                        Actions rapides
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-2"></i>
                            Edit book
                        </a>
                        @if($book->file)
                            <a href="{{ $book->file_url }}" target="_blank" class="btn btn-outline-info">
                                <i data-feather="download" class="me-2"></i>
                                Download
                            </a>
                        @endif
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="list" class="me-2"></i>
                            Back to list
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
