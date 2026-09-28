@extends('student-dashboard.layouts.app')

@section('title', __('messages.certificates'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.certificates') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.certificates') }}</h1>
            <p class="text-muted">{{ __('messages.your_earned_certificates') }}</p>
        </div>
        <div>
            @if($certificates->count() > 0)
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>{{ __('messages.print_all') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $certificateStats['total_certificates'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.total_certificates') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $certificateStats['total_certificates'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.approved_certificates') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $certificateStats['pending_certificates'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.pending_certificates') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-calendar"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $certificateStats['this_month'] }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.this_month') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student-dashboard.certificates.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_status') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('messages.approved') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> {{ __('messages.filter') }}
                            </button>
                            <a href="{{ route('student-dashboard.certificates.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> {{ __('messages.clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Certificates Grid -->
    @if($certificates->count() > 0)
        <div class="row">
            @foreach($certificates as $certificate)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100 certificate-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-trophy text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="card-title">{{ $certificate->product->titre ?? __('messages.course_certificate') }}</h5>
                            <p class="text-muted">{{ __('messages.course_completion_certificate') }}</p>

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <small class="text-muted">{{ __('messages.issue_date') }}</small>
                                    <div class="fw-bold">
                                        @if($certificate->validated_at)
                                            {{ $certificate->validated_at->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                        @else
                                            {{ __('messages.not_issued') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">{{ __('messages.status') }}</small>
                                    <div class="fw-bold">
                                        <span class="badge bg-{{ $certificate->status_color }}">
                                            {{ $certificate->status_text }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                @if($certificate->lien_video)
                                    <span class="badge bg-info">{{ __('messages.with_video') }}</span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                @if($certificate->isApproved())
                                    @php
                                        $certifStudent = $certificate->certificates()->first();
                                    @endphp
                                    @if($certifStudent && $certifStudent->file_path)
                                        <a href="{{ $certifStudent->file_url }}" target="_blank" class="btn btn-primary btn-sm flex-fill" download>
                                            <i class="bi bi-download me-1"></i>{{ __('messages.download') }}
                                        </a>
                                    @else
                                        <a href="{{ route('student-dashboard.certificates.show', $certificate->id) }}" class="btn btn-primary btn-sm flex-fill">
                                            <i class="bi bi-download me-1"></i>{{ __('messages.download') }}
                                        </a>
                                    @endif
                                @else
                                    <button class="btn btn-outline-secondary btn-sm flex-fill" disabled>
                                        <i class="bi bi-lock me-1"></i>{{ __('messages.not_available') }}
                                    </button>
                                @endif

                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#certificateModal{{ $certificate->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($certificate->isApproved())
                                    <button class="btn btn-outline-info btn-sm" onclick="shareCertificate({{ $certificate->id }})">
                                        <i class="bi bi-share"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificate Details Modal -->
                <div class="modal fade" id="certificateModal{{ $certificate->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('messages.certificate_details') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                        <i class="bi bi-trophy text-primary" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4>{{ $certificate->product->titre ?? __('messages.course_certificate') }}</h4>
                                    <p class="text-muted">{{ __('messages.course_completion_certificate') }}</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('messages.certificate_info') }}</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('messages.issue_date') }}:</strong> {{ $certificate->validated_at ? $certificate->validated_at->format('d M Y') : __('messages.not_issued') }}</li>
                                            <li><strong>{{ __('messages.submitted_at') }}:</strong> {{ $certificate->submitted_at ? $certificate->submitted_at->format('d M Y') : __('messages.not_submitted') }}</li>
                                            <li><strong>{{ __('messages.certificate_id') }}:</strong> CERT-{{ $certificate->id }}-{{ $certificate->product_id }}</li>
                                            <li><strong>{{ __('messages.status') }}:</strong>
                                                <span class="badge bg-{{ $certificate->status_color }}">
                                                    {{ $certificate->status_text }}
                                                </span>
                                            </li>
                                            @if($certificate->admin_notes)
                                                <li><strong>{{ __('messages.admin_notes') }}:</strong> {{ $certificate->admin_notes }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('messages.course_details') }}</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('messages.course_name') }}:</strong> {{ $certificate->product->titre ?? __('messages.unknown_course') }}</li>
                                            <li><strong>{{ __('messages.course_id') }}:</strong> {{ $certificate->product_id }}</li>
                                            @if($certificate->lien_video)
                                                <li><strong>{{ __('messages.video_exam') }}:</strong> <span class="text-success">{{ __('messages.completed') }}</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                @if($certificate->lien_video)
                                    <div class="mt-4">
                                        <h6>{{ __('messages.video_exam_link') }}</h6>
                                        <div class="alert alert-info">
                                            <i class="bi bi-play-circle me-2"></i>
                                            <a href="{{ $certificate->lien_video }}" target="_blank" class="text-decoration-none">
                                                {{ __('messages.watch_video_exam') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                @if($certificate->isApproved())
                                    @php
                                        $certifStudent = $certificate->certificates()->first();
                                    @endphp
                                    @if($certifStudent && $certifStudent->file_path)
                                        <a href="{{ $certifStudent->file_url }}" target="_blank" class="btn btn-primary" download>{{ __('messages.download_certificate') }}</a>
                                    @else
                                        <a href="{{ route('student-dashboard.certificates.show', $certificate->id) }}" class="btn btn-primary">{{ __('messages.download_certificate') }}</a>
                                    @endif
                                    <button class="btn btn-info" onclick="shareCertificate({{ $certificate->id }})">{{ __('messages.share_certificate') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $certificates->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-trophy fs-1 text-muted mb-3"></i>
            <h4 class="text-muted">{{ __('messages.no_certificates_found') }}</h4>
            <p class="text-muted">{{ __('messages.no_certificates_earned_yet') }}</p>
            <a href="{{ route('student-dashboard.courses.index') }}" class="btn btn-primary">
                <i class="bi bi-book me-2"></i>{{ __('messages.browse_courses') }}
            </a>
        </div>
    @endif
</div>

<script>
function shareCertificate(certificateId) {
    if (navigator.share) {
        navigator.share({
            title: '{{ __('messages.my_certificate') }}',
            text: '{{ __('messages.check_out_my_certificate') }}',
            url: window.location.origin + '/student-dashboard/certificates/' + certificateId
        });
    } else {
        const url = window.location.origin + '/student-dashboard/certificates/' + certificateId;
        navigator.clipboard.writeText(url).then(() => {
            alert('{{ __('messages.certificate_link_copied') }}');
        });
    }
}
</script>
@endsection

@push('styles')
<style>
.certificate-card {
    transition: transform 0.2s ease-in-out;
}

.certificate-card:hover {
    transform: translateY(-5px);
}

.stat-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

@media print {
    .btn, .modal, nav, .breadcrumb {
        display: none !important;
    }

    .certificate-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>
@endpush
