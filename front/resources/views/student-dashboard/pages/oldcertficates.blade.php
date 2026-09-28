@extends('student-dashboard.layouts.app')

@php
    $oldCertifPublicUrl = function (?string $relative): ?string {
        if (! $relative) {
            return null;
        }
        $normalized = str_replace('\\', '/', $relative);
        $parts = preg_split('#/#', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if (! $parts) {
            return null;
        }

        return 'https://files.upskillsacademy.com/certificates/' . implode('/', array_map('rawurlencode', $parts));
    };
@endphp

@section('title', __('messages.certificates'))

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.certificates') }}</li>
        </ol>
    </nav>

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

    @if($certificates->count() > 0)
        <div class="row">
            @foreach($certificates as $certificate)
                @php
                    $isApproved = (($certificate['active'] ?? '1') === '1');
                    $issueRaw = $certificate['date'] ?? $certificate['created_at'] ?? null;
                    try {
                        $issueLabel = $issueRaw ? \Carbon\Carbon::parse($issueRaw)->format('d M Y') : __('messages.not_issued');
                    } catch (\Throwable $e) {
                        $issueLabel = __('messages.not_issued');
                    }
                    $fileHref = $oldCertifPublicUrl($certificate['pdf'] ?? null) ?? $oldCertifPublicUrl($certificate['image'] ?? null);
                    $title = $certificate['course_name'] ?? $certificate['exam_name'] ?? __('messages.course_certificate');
                @endphp
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100 certificate-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-trophy text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="card-title">{{ $title }}</h5>
                            <p class="text-muted">{{ __('messages.course_completion_certificate') }}</p>

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <small class="text-muted">{{ __('messages.issue_date') }}</small>
                                    <div class="fw-bold">{{ $issueLabel }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">{{ __('messages.status') }}</small>
                                    <div class="fw-bold">
                                        @if($isApproved)
                                            <span class="badge bg-success">{{ __('messages.approved') }}</span>
                                        @elseif(($certificate['active'] ?? '') === '0')
                                            <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                @if($isApproved)
                                    <span class="badge bg-success">{{ __('messages.validated') }}</span>
                                @elseif(($certificate['active'] ?? '') === '0')
                                    <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                @if($isApproved && $fileHref)
                                    <a href="{{ $fileHref }}" target="_blank" class="btn btn-primary btn-sm flex-fill" rel="noopener noreferrer">
                                        <i class="bi bi-download me-1"></i>{{ __('messages.download') }}
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm flex-fill" type="button" disabled>
                                        <i class="bi bi-lock me-1"></i>{{ __('messages.not_available') }}
                                    </button>
                                @endif

                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#certificateModal{{ $certificate['id'] }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                @if($isApproved && $fileHref)
                                    <button class="btn btn-outline-info btn-sm" type="button" onclick="shareOldCertificate(@js($fileHref))">
                                        <i class="bi bi-share"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="certificateModal{{ $certificate['id'] }}" tabindex="-1">
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
                                    <h4>{{ $title }}</h4>
                                    <p class="text-muted">{{ __('messages.course_completion_certificate') }}</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('messages.certificate_info') }}</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('messages.issue_date') }}:</strong> {{ $issueLabel }}</li>
                                            <li><strong>{{ __('messages.certificate_id') }}:</strong> {{ $certificate['serialnumber'] ?? ('OLD-' . ($certificate['id'] ?? '')) }}</li>
                                            <li><strong>{{ __('messages.status') }}:</strong>
                                                @if($isApproved)
                                                    <span class="badge bg-success">{{ __('messages.approved') }}</span>
                                                @elseif(($certificate['active'] ?? '') === '0')
                                                    <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('messages.course_details') }}</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('messages.course_name') }}:</strong> {{ $certificate['course_name'] ?? __('messages.unknown_course') }}</li>
                                            @if(isset($certificate['course_id']))
                                                <li><strong>{{ __('messages.course_id') }}:</strong> {{ $certificate['course_id'] }}</li>
                                            @endif
                                            @if(!empty($certificate['exam_name']))
                                                <li><strong>{{ __('messages.video_exam') }}:</strong> {{ $certificate['exam_name'] }}</li>
                                            @endif
                                            @if(!empty($certificate['teacher_name']))
                                                <li>{{ $certificate['teacher_name'] }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                @if($isApproved && $fileHref)
                                    <a href="{{ $fileHref }}" target="_blank" class="btn btn-primary" rel="noopener noreferrer">{{ __('messages.download_certificate') }}</a>
                                    <button class="btn btn-info" type="button" onclick="shareOldCertificate(@js($fileHref))">{{ __('messages.share_certificate') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

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
function shareOldCertificate(fileUrl) {
    if (navigator.share) {
        navigator.share({
            title: '{{ __('messages.my_certificate') }}',
            text: '{{ __('messages.check_out_my_certificate') }}',
            url: fileUrl
        });
    } else {
        navigator.clipboard.writeText(fileUrl).then(() => {
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
