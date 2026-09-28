@extends('layouts.app')

@section('title', 'Create Affiliate Partner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fa fa-plus me-2"></i>
                        Create New Affiliate Partner
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('affiliate-partners.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-user me-2"></i>Basic Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                <i class="fa fa-user-tag me-2"></i>Full Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                <i class="fa fa-envelope me-2"></i>Email Address <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">
                                                <i class="fa fa-phone me-2"></i>Phone Number
                                            </label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone') }}" placeholder="+33 1 23 45 67 89">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="company" class="form-label">
                                                <i class="fa fa-building me-2"></i>Company Name
                                            </label>
                                            <input type="text" class="form-control @error('company') is-invalid @enderror"
                                                   id="company" name="company" value="{{ old('company') }}">
                                            @error('company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="website" class="form-label">
                                                <i class="fa fa-globe me-2"></i>Website URL
                                            </label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commission & Payment -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-money-bill me-2"></i>Commission & Payment
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="commission_rate" class="form-label">
                                                <i class="fa fa-percentage me-2"></i>Commission Rate (%) <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control @error('commission_rate') is-invalid @enderror"
                                                   id="commission_rate" name="commission_rate" value="{{ old('commission_rate', 10) }}"
                                                   step="0.5" min="0" max="100" required>
                                            @error('commission_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Percentage earned per successful referral.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">
                                                <i class="fa fa-credit-card me-2"></i>Payment Method <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror"
                                                    id="payment_method" name="payment_method" required>
                                                <option value="">Select method</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3" id="payment_details_section">
                                            <label for="payment_details" class="form-label">
                                                <i class="fa fa-file-invoice me-2"></i>Payment Details
                                            </label>
                                            <textarea class="form-control @error('payment_details') is-invalid @enderror"
                                                      id="payment_details" name="payment_details" rows="4"
                                                      placeholder="Bank account, IBAN, PayPal email, etc.">{{ old('payment_details') }}</textarea>
                                            @error('payment_details')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="payout_threshold" class="form-label">
                                                <i class="fa fa-coins me-2"></i>Payout Threshold ($)
                                            </label>
                                            <input type="number" class="form-control @error('payout_threshold') is-invalid @enderror"
                                                   id="payout_threshold" name="payout_threshold" value="{{ old('payout_threshold', 50) }}"
                                                   step="1" min="0">
                                            @error('payout_threshold')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Minimum amount before payout is triggered.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing & Social Media -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-bullhorn me-2"></i>Marketing Channels
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="marketing_channels" class="form-label">
                                                <i class="fa fa-ad me-2"></i>Primary Marketing Channels
                                            </label>
                                            <select class="form-select @error('marketing_channels') is-invalid @enderror"
                                                    id="marketing_channels" name="marketing_channels[]" multiple size="6">
                                                <option value="blog" {{ in_array('blog', old('marketing_channels', [])) ? 'selected' : '' }}>Blog/Website</option>
                                                <option value="youtube" {{ in_array('youtube', old('marketing_channels', [])) ? 'selected' : '' }}>YouTube</option>
                                                <option value="instagram" {{ in_array('instagram', old('marketing_channels', [])) ? 'selected' : '' }}>Instagram</option>
                                                <option value="facebook" {{ in_array('facebook', old('marketing_channels', [])) ? 'selected' : '' }}>Facebook</option>
                                                <option value="twitter" {{ in_array('twitter', old('marketing_channels', [])) ? 'selected' : '' }}>Twitter/X</option>
                                                <option value="linkedin" {{ in_array('linkedin', old('marketing_channels', [])) ? 'selected' : '' }}>LinkedIn</option>
                                                <option value="email" {{ in_array('email', old('marketing_channels', [])) ? 'selected' : '' }}>Email Marketing</option>
                                                <option value="podcast" {{ in_array('podcast', old('marketing_channels', [])) ? 'selected' : '' }}>Podcast</option>
                                                <option value="other" {{ in_array('other', old('marketing_channels', [])) ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('marketing_channels')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Hold Ctrl/Cmd to select multiple channels.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="audience_size" class="form-label">
                                                <i class="fa fa-users me-2"></i>Estimated Audience Size
                                            </label>
                                            <select class="form-select @error('audience_size') is-invalid @enderror"
                                                    id="audience_size" name="audience_size">
                                                <option value="">Select range</option>
                                                <option value="0-1k" {{ old('audience_size') == '0-1k' ? 'selected' : '' }}>0 - 1,000</option>
                                                <option value="1k-10k" {{ old('audience_size') == '1k-10k' ? 'selected' : '' }}>1,000 - 10,000</option>
                                                <option value="10k-50k" {{ old('audience_size') == '10k-50k' ? 'selected' : '' }}>10,000 - 50,000</option>
                                                <option value="50k-100k" {{ old('audience_size') == '50k-100k' ? 'selected' : '' }}>50,000 - 100,000</option>
                                                <option value="100k+" {{ old('audience_size') == '100k+' ? 'selected' : '' }}>100,000+</option>
                                            </select>
                                            @error('audience_size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-share-alt me-2"></i>Social Media Profiles
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="social_media_facebook" class="form-label">
                                                <i class="fab fa-facebook me-2"></i>Facebook URL
                                            </label>
                                            <input type="url" class="form-control @error('social_media.facebook') is-invalid @enderror"
                                                   id="social_media_facebook" name="social_media[facebook]"
                                                   value="{{ old('social_media.facebook') }}" placeholder="https://facebook.com/...">
                                            @error('social_media.facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="social_media_instagram" class="form-label">
                                                <i class="fab fa-instagram me-2"></i>Instagram URL
                                            </label>
                                            <input type="url" class="form-control @error('social_media.instagram') is-invalid @enderror"
                                                   id="social_media_instagram" name="social_media[instagram]"
                                                   value="{{ old('social_media.instagram') }}" placeholder="https://instagram.com/...">
                                            @error('social_media.instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="social_media_twitter" class="form-label">
                                                <i class="fab fa-twitter me-2"></i>Twitter/X URL
                                            </label>
                                            <input type="url" class="form-control @error('social_media.twitter') is-invalid @enderror"
                                                   id="social_media_twitter" name="social_media[twitter]"
                                                   value="{{ old('social_media.twitter') }}" placeholder="https://twitter.com/...">
                                            @error('social_media.twitter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="social_media_linkedin" class="form-label">
                                                <i class="fab fa-linkedin me-2"></i>LinkedIn URL
                                            </label>
                                            <input type="url" class="form-control @error('social_media.linkedin') is-invalid @enderror"
                                                   id="social_media_linkedin" name="social_media[linkedin]"
                                                   value="{{ old('social_media.linkedin') }}" placeholder="https://linkedin.com/in/...">
                                            @error('social_media.linkedin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="social_media_youtube" class="form-label">
                                                <i class="fab fa-youtube me-2"></i>YouTube Channel URL
                                            </label>
                                            <input type="url" class="form-control @error('social_media.youtube') is-invalid @enderror"
                                                   id="social_media_youtube" name="social_media[youtube]"
                                                   value="{{ old('social_media.youtube') }}" placeholder="https://youtube.com/...">
                                            @error('social_media.youtube')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-info-circle me-2"></i>Additional Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">
                                                <i class="fa fa-sticky-note me-2"></i>Internal Notes
                                            </label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                                      id="notes" name="notes" rows="4"
                                                      placeholder="Any internal notes or comments about this partner...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Note:</strong> New partners will be created with "Pending" status and require manual approval before they can start earning commissions.
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
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save me-2"></i>Create Partner
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update payment details placeholder based on selected method
    const paymentMethod = document.getElementById('payment_method');
    const paymentDetails = document.getElementById('payment_details');

    paymentMethod.addEventListener('change', function() {
        const placeholders = {
            'bank_transfer': 'Bank Name:\nIBAN:\nBIC/SWIFT:\nAccount Holder:',
            'paypal': 'PayPal Email Address',
            'stripe': 'Stripe Account ID or Email',
            'check': 'Mailing Address for Checks'
        };

        paymentDetails.placeholder = placeholders[this.value] || 'Enter payment details...';
    });
});
</script>
@endpush
