@extends('layouts.app')

@section('title', 'Edit Affiliate Partner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-edit me-2"></i>
                        Edit Partner: {{ $partner->name }}
                    </h4>
                    <span class="badge bg-light text-dark fs-6">
                        Status: {{ ucfirst($partner->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('affiliate-partners.update', $partner) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Status Management (Admin Only) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fa fa-shield-alt me-2"></i>Status Management</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Partner Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                    <option value="pending" {{ old('status', $partner->status) == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                                    <option value="approved" {{ old('status', $partner->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="approved" {{ old('status', $partner->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="suspended" {{ old('status', $partner->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info mb-0">
                                                    <small>
                                                        <strong>Joined:</strong> {{ $partner->created_at->format('d/m/Y') }}<br>
                                                        <strong>Total Earned:</strong> {{ number_format($partner->total_earned ?? 0, 2) }} $
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Same form fields as create.blade.php but with values pre-filled -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-user me-2"></i>Basic Information</h5></div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $partner->name) }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $partner->email) }}" required>
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone', $partner->phone) }}">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="company" class="form-label">Company</label>
                                            <input type="text" class="form-control @error('company') is-invalid @enderror"
                                                   id="company" name="company" value="{{ old('company', $partner->company) }}">
                                            @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="website" name="website" value="{{ old('website', $partner->website) }}">
                                            @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-money-bill me-2"></i>Commission & Payment</h5></div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="commission_rate" class="form-label">Commission Rate (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('commission_rate') is-invalid @enderror"
                                                   id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $partner->commission_rate) }}"
                                                   step="0.5" min="0" max="100" required>
                                            @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror"
                                                    id="payment_method" name="payment_method" required>
                                                <option value="">Select method</option>
                                                <option value="bank_transfer" {{ old('payment_method', $partner->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="paypal" {{ old('payment_method', $partner->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="stripe" {{ old('payment_method', $partner->payment_method) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                                <option value="check" {{ old('payment_method', $partner->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                            </select>
                                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment_details" class="form-label">Payment Details</label>
                                            <textarea class="form-control @error('payment_details') is-invalid @enderror"
                                                      id="payment_details" name="payment_details" rows="4">{{ old('payment_details', $partner->payment_details) }}</textarea>
                                            @error('payment_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="payout_threshold" class="form-label">Payout Threshold ($)</label>
                                            <input type="number" class="form-control @error('payout_threshold') is-invalid @enderror"
                                                   id="payout_threshold" name="payout_threshold" value="{{ old('payout_threshold', $partner->payout_threshold) }}"
                                                   step="1" min="0">
                                            @error('payout_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing & Social (Condensed) -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-bullhorn me-2"></i>Marketing Channels</h5></div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="marketing_channels" class="form-label">Primary Channels</label>
                                            @php $selectedChannels = old('marketing_channels', $partner->marketing_channels ? json_decode($partner->marketing_channels, true) : []); @endphp
                                            <select class="form-select" id="marketing_channels" name="marketing_channels[]" multiple size="5">
                                                <option value="blog" {{ in_array('blog', $selectedChannels) ? 'selected' : '' }}>Blog/Website</option>
                                                <option value="youtube" {{ in_array('youtube', $selectedChannels) ? 'selected' : '' }}>YouTube</option>
                                                <option value="instagram" {{ in_array('instagram', $selectedChannels) ? 'selected' : '' }}>Instagram</option>
                                                <option value="facebook" {{ in_array('facebook', $selectedChannels) ? 'selected' : '' }}>Facebook</option>
                                                <option value="email" {{ in_array('email', $selectedChannels) ? 'selected' : '' }}>Email Marketing</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="audience_size" class="form-label">Audience Size</label>
                                            <select class="form-select" id="audience_size" name="audience_size">
                                                <option value="">Select range</option>
                                                <option value="0-1k" {{ old('audience_size', $partner->audience_size) == '0-1k' ? 'selected' : '' }}>0 - 1,000</option>
                                                <option value="1k-10k" {{ old('audience_size', $partner->audience_size) == '1k-10k' ? 'selected' : '' }}>1,000 - 10,000</option>
                                                <option value="10k-50k" {{ old('audience_size', $partner->audience_size) == '10k-50k' ? 'selected' : '' }}>10,000 - 50,000</option>
                                                <option value="100k+" {{ old('audience_size', $partner->audience_size) == '100k+' ? 'selected' : '' }}>100,000+</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Notes</h5></div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Internal Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="7">{{ old('notes', $partner->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('affiliate-partners.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-save me-2"></i>Update Partner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
