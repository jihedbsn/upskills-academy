@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.my_books'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.my_books') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.my_books') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.available_books') }}</p>
        </div>

    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $bookStats['total_books'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.total_books') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Books Grid -->
    <div class="row">
        @forelse($purchasedBookOrders as $order)
            @php
                $book = $order->book;
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="{{ $book->image_url }}" class="card-img-top" alt="{{ $book->titre }}">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">{{ __('student_dashboard.purchased') }}</span>
                        </div>
                        <div class="position-absolute top-0 start-0 m-2">
                            <div class="bg-white rounded-circle p-1">
                                <i class="bi bi-star-fill text-warning"></i>
                                <small class="ms-1">@price($order->price)</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $book->titre }}</h6>
                        <p class="text-muted small mb-2">{{ __('student_dashboard.by') }} {{ __('student_dashboard.author') }}</p>
                        <p class="card-text small">{{ $book->description_short }}</p>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                @price($order->price)
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ __('student_dashboard.purchased') }}: {{ $order->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            @if($book->file_url)
                                <a href="{{ $book->file_url }}" class="btn btn-primary btn-sm flex-fill" target="_blank">
                                    <i class="bi bi-download me-1"></i>{{ __('student_dashboard.download') }}
                                </a>
                            @else
                                <a href="#" class="btn btn-secondary btn-sm flex-fill disabled">
                                    <i class="bi bi-file-earmark me-1"></i>{{ __('student_dashboard.no_file') }}
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-book display-1 text-muted"></i>
                    <h4 class="mt-3">{{ __('student_dashboard.no_books_purchased') }}</h4>
                    <p class="text-muted">{{ __('student_dashboard.purchase_your_first_book') }}</p>

                </div>
            </div>
        @endforelse
    </div>


</div>



<!-- Book Details Modal -->
<div class="modal fade" id="bookDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.book_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="https://via.placeholder.com/300x400/6366f1/ffffff?text=Book+Cover" class="img-fluid rounded" alt="Book Cover">
                    </div>
                    <div class="col-md-8">
                        <h5>{{ __('student_dashboard.python_complete_guide') }}</h5>
                        <p class="text-muted">{{ __('student_dashboard.by') }} John Smith</p>

                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <span class="text-muted">4.9 (128 {{ __('student_dashboard.reviews') }})</span>
                            </div>
                        </div>

                        <p>{{ __('student_dashboard.comprehensive_python_guide_description') }}</p>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>{{ __('student_dashboard.pages') }}:</strong> 450
                            </div>
                            <div class="col-6">
                                <strong>{{ __('student_dashboard.language') }}:</strong> {{ __('student_dashboard.english') }}
                            </div>
                            <div class="col-6">
                                <strong>{{ __('student_dashboard.published') }}:</strong> 2023
                            </div>
                            <div class="col-6">
                                <strong>{{ __('student_dashboard.isbn') }}:</strong> 978-1234567890
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>{{ __('student_dashboard.table_of_contents') }}</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success me-2"></i>{{ __('student_dashboard.introduction_to_python') }}</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>{{ __('student_dashboard.variables_and_data_types') }}</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>{{ __('student_dashboard.control_flow') }}</li>
                                <li><i class="bi bi-circle text-muted me-2"></i>{{ __('student_dashboard.functions_and_modules') }}</li>
                                <li><i class="bi bi-circle text-muted me-2"></i>{{ __('student_dashboard.object_oriented_programming') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.close') }}</button>
                <a href="#" class="btn btn-primary">{{ __('student_dashboard.read_now') }}</a>
                <a href="#" class="btn btn-success">{{ __('student_dashboard.download') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
