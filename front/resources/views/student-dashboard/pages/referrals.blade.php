@extends('student-dashboard.layouts.app')

@section('title', __('messages.referrals'))

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard', ['default' => 'Dashboard']) }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.referrals') }}</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.referrals') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.referrals_subtitle') }}</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Hero Card: Referral Link --}}
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body p-4 text-white">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-2">
                        <i class="bi bi-gift-fill me-2"></i>{{ __('messages.referrals_hero_title') }}
                    </h3>
                    <p class="mb-3 opacity-75">
                        {!! __('messages.referrals_hero_body') !!}
                    </p>

                    @if($stats['code'])
                        <div class="input-group mb-2" style="max-width: 540px;">
                            <input type="text" id="referralLinkInput" class="form-control fw-semibold"
                                value="{{ $referralLink }}" readonly>
                            <button class="btn btn-light fw-bold" type="button" id="copyBtn" onclick="copyReferralLink()">
                                <i class="bi bi-clipboard me-1"></i> {{ __('messages.copy') }}
                            </button>
                        </div>
                        <small class="opacity-75 d-block">{{ __('messages.your_code') }}:
                            <span class="badge bg-white text-dark fw-bold ms-1" style="font-size: 0.85rem;">{{ $stats['code'] }}</span>
                        </small>

                        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center share-buttons">
                            <span class="small opacity-75 me-1">{{ __('messages.share_on') }}</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm btn-light rounded-circle share-btn" title="Facebook" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode(__('messages.referrals_share_text') . ' ' . $referralLink) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm btn-light rounded-circle share-btn" title="WhatsApp" aria-label="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($referralLink) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm btn-light rounded-circle share-btn" title="LinkedIn" aria-label="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <button type="button" onclick="shareOnInstagram()"
                                class="btn btn-sm btn-light rounded-circle share-btn" title="Instagram" aria-label="Instagram">
                                <i class="bi bi-instagram"></i>
                            </button>
                            <button type="button" onclick="copyReferralLink()"
                                class="btn btn-sm btn-light rounded-circle share-btn" title="{{ __('messages.copy') }}" aria-label="{{ __('messages.copy') }}">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    @else
                        <em class="opacity-75">{{ __('messages.referral_code_generating') }}</em>
                    @endif
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="bi bi-people-fill" style="font-size: 6rem; opacity: 0.25;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(102,126,234,0.15); color: #667eea;">
                            <i class="bi bi-person-plus-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.friends_invited') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['invited'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(40,167,69,0.15); color: #28a745;">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.completed') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['completed'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(255,159,67,0.15); color: #ff9f43;">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.pending') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(40,167,69,0.15); color: #28a745;">
                            <i class="bi bi-cash-coin fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.rewards_earned') }}</div>
                            <div class="h4 mb-0 fw-bold">${{ number_format($stats['earnedRewards'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(220,53,69,0.15); color: #dc3545;">
                            <i class="bi bi-hourglass fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.pending_rewards') }}</div>
                            <div class="h4 mb-0 fw-bold">${{ number_format($stats['pendingRewards'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($stats['creditBalance']) && $stats['creditBalance'] > 0)
        <div class="col-md-6 col-lg">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #d1f7d3 0%, #b8e6c1 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: rgba(40,167,69,0.25); color: #198754;">
                            <i class="bi bi-coin fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">{{ __('messages.credit_balance') }}</div>
                            <div class="h4 mb-0 fw-bold text-success">${{ number_format($stats['creditBalance'], 2) }}</div>
                            <small class="text-muted">{{ __('messages.auto_applied_at_checkout') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Reward Preference + Referral History --}}
    <div class="row g-3 mb-4">

        {{-- Reward Preference --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-toggles me-2" style="color: #0071ae;"></i>{{ __('messages.reward_preference') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ __('messages.reward_preference_intro') }}</p>

                    <form method="POST" action="{{ route('student-dashboard.referrals.preference') }}">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="w-100" style="cursor: pointer;">
                                    <input type="radio" name="type" value="cash" class="d-none reward-radio"
                                        {{ $stats['rewardType'] === 'cash' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <div class="border rounded p-3 text-center reward-card {{ $stats['rewardType'] === 'cash' ? 'border-primary bg-light' : '' }}">
                                        <i class="bi bi-cash-stack fs-2 d-block mb-2 {{ $stats['rewardType'] === 'cash' ? 'text-primary' : 'text-muted' }}"></i>
                                        <div class="fw-bold">{{ __('messages.reward_cash') }}</div>
                                        <small class="text-muted d-block">{{ __('messages.reward_cash_short') }}</small>
                                        <small class="text-muted">{{ __('messages.reward_cash_sub') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="w-100" style="cursor: pointer;">
                                    <input type="radio" name="type" value="credit" class="d-none reward-radio"
                                        {{ $stats['rewardType'] === 'credit' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <div class="border rounded p-3 text-center reward-card {{ $stats['rewardType'] === 'credit' ? 'border-primary bg-light' : '' }}">
                                        <i class="bi bi-coin fs-2 d-block mb-2 {{ $stats['rewardType'] === 'credit' ? 'text-primary' : 'text-muted' }}"></i>
                                        <div class="fw-bold">{{ __('messages.reward_credit') }}</div>
                                        <small class="text-muted d-block">{{ __('messages.reward_credit_short') }}</small>
                                        <small class="text-muted">{{ __('messages.reward_credit_sub') }}</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 mb-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            {!! __('messages.referral_cash_credit_help') !!}
                        </div>

                        <div class="alert alert-success py-2 mb-0 small">
                            <i class="bi bi-check-circle me-1"></i>
                            {!! __('messages.currently_set_to', ['type' => '<strong>' . ($stats['rewardType'] === 'cash' ? __('messages.reward_cash') : __('messages.reward_credit')) . '</strong>']) !!}
                            {{ __('messages.changes_apply_to_unclaimed') }}
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Referral History --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2" style="color: #0071ae;"></i>{{ __('messages.referral_history') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    @php $referrals = $history['data'] ?? []; @endphp

                    @if(empty($referrals))
                        <div class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem; opacity: 0.4;"></i>
                            <p class="text-muted mt-3 mb-0">{{ __('messages.no_referrals_yet') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{{ __('messages.friend') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.reward') }}</th>
                                        <th class="pe-3">{{ __('messages.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($referrals as $referral)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2"
                                                    style="width: 36px; height: 36px; background: linear-gradient(135deg,#667eea,#764ba2); font-size: 0.85rem;">
                                                    {{ strtoupper(substr($referral['referred']['first_name'] ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold small">
                                                        {{ trim(($referral['referred']['first_name'] ?? '') . ' ' . ($referral['referred']['last_name'] ?? '')) ?: __('messages.user') }}
                                                    </div>
                                                    <div class="text-muted small">{{ $referral['referred']['email'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($referral['status'] === 'completed')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>{{ __('messages.completed') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i>{{ __('messages.pending') }}
                                                </span>
                                            @endif
                                            <div class="progress mt-2" style="height: 4px; max-width: 120px;">
                                                <div class="progress-bar {{ $referral['status'] === 'completed' ? 'bg-success' : 'bg-warning' }}"
                                                    style="width: {{ $referral['status'] === 'completed' ? '100' : '40' }}%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $referrerReward = collect($referral['rewards'] ?? [])->firstWhere('role', 'referrer');
                                            @endphp
                                            @if($referrerReward)
                                                <div class="fw-bold">${{ number_format($referrerReward['amount'], 2) }}</div>
                                                <small class="text-muted">{{ $referrerReward['type'] === 'cash' ? __('messages.reward_cash') : __('messages.reward_credit') }}</small>
                                                @if(!$referrerReward['claimed_at'])
                                                    <form method="POST" action="{{ route('student-dashboard.referrals.claim') }}" class="mt-1">
                                                        @csrf
                                                        <input type="hidden" name="reward_id" value="{{ $referrerReward['id'] }}">
                                                        <button type="submit" class="btn btn-sm btn-success py-0 px-2" style="font-size: 0.7rem;">
                                                            {{ __('messages.claim') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="small text-success"><i class="bi bi-check-circle"></i> {{ __('messages.claimed') }}</div>
                                                @endif
                                            @else
                                                <small class="text-muted">{{ __('messages.pending_purchase') }}</small>
                                            @endif
                                        </td>
                                        <td class="pe-3 small text-muted">
                                            {{ \Carbon\Carbon::parse($referral['created_at'])->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-question-circle me-2" style="color: #0071ae;"></i>{{ __('messages.how_it_works') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px; background: rgba(102,126,234,0.15); color: #667eea;">
                        <span class="fs-3 fw-bold">1</span>
                    </div>
                    <h6 class="fw-bold">{{ __('messages.referral_step1_title') }}</h6>
                    <p class="text-muted small mb-0">{{ __('messages.referral_step1_body') }}</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px; background: rgba(102,126,234,0.15); color: #667eea;">
                        <span class="fs-3 fw-bold">2</span>
                    </div>
                    <h6 class="fw-bold">{{ __('messages.referral_step2_title') }}</h6>
                    <p class="text-muted small mb-0">{{ __('messages.referral_step2_body') }}</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px; background: rgba(102,126,234,0.15); color: #667eea;">
                        <span class="fs-3 fw-bold">3</span>
                    </div>
                    <h6 class="fw-bold">{{ __('messages.referral_step3_title') }}</h6>
                    <p class="text-muted small mb-0">{{ __('messages.referral_step3_body') }}</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 60px; height: 60px; background: rgba(40,167,69,0.15); color: #28a745;">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <h6 class="fw-bold">{{ __('messages.referral_step4_title') }}</h6>
                    <p class="text-muted small mb-0">{{ __('messages.referral_step4_body') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referralLinkInput');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    if (navigator.clipboard) {
        navigator.clipboard.writeText(input.value).then(showCopied);
    } else {
        document.execCommand('copy');
        showCopied();
    }
}

function shareOnInstagram() {
    const input = document.getElementById('referralLinkInput');
    if (!input) return;
    const link = input.value;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(link).catch(() => {});
    }
    alert(@json(__('messages.instagram_share_hint')));
    window.open('https://www.instagram.com/', '_blank', 'noopener');
}

function showCopied() {
    const btn = document.getElementById('copyBtn');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2 me-1"></i> {{ __('messages.copied') }}!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-light');
    setTimeout(() => {
        btn.innerHTML = original;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-light');
    }, 2000);
}
</script>

<style>
.reward-card { transition: all 0.2s ease; }
.reward-card:hover { border-color: #0071ae !important; transform: translateY(-2px); }
.share-btn { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; font-size: 1rem; transition: transform 0.15s ease; }
.share-btn:hover { transform: translateY(-2px); }
</style>
@endsection
