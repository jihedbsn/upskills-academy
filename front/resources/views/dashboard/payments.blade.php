@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.payments') }}</h4>
          </div>

          @if(empty($payments))
            <div class="text-center py-50">
              <i class="text-60 icon-credit-card text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_payments_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.purchase_courses_message') }}</p>
              <a href="{{ route('accueil.index') }}" class="button -md -orange-1 text-white mt-20">
                {{ __('messages.browse_courses') }}
              </a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.course') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($payments as $payment)
                    <tr>
                      <td>{{ $payment->date }}</td>
                      <td>{{ $payment->course_name }}</td>
                      <td>{{ $payment->amount }}</td>
                      <td>
                        <span class="badge {{ $payment->status == 'completed' ? 'bg-green-1' : 'bg-orange-1' }}">
                          {{ $payment->status }}
                        </span>
                      </td>
                      <td>
                        <a href="#" class="button -sm -purple-1 text-white">{{ __('messages.view') }}</a>
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
</div>
@endsection
