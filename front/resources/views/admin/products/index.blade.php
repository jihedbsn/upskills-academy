@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.courses_management') }}</h4>
            <a href="{{ route('admin.products.create') }}" class="button -md -purple-1 text-white">
              <i class="icon-plus mr-10"></i>
              {{ __('messages.create_new_course') }}
            </a>
          </div>

          @if(session('success'))
            <div class="alert alert-success mb-30">
              {{ session('success') }}
            </div>
          @endif

          @if(empty($products))
            <div class="text-center py-50">
              <i class="text-60 icon-play-button text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_courses_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.create_your_first_course') }}</p>
              <a href="{{ route('admin.products.create') }}" class="button -md -purple-1 text-white mt-20">
                {{ __('messages.create_course') }}
              </a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ __('messages.image') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.teacher') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($products as $product)
                    <tr>
                      <td>
                      @if($product->image)
                          <img src="{{ config('app.file_url') }}uploads/products/images/{{ $product->image }}" 
                               alt="{{ $product->titre }}" 
                               class="size-60 rounded-8 object-cover"
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                      @endif
                      <div class="size-60 rounded-8 bg-light-3 d-flex items-center justify-center" 
                           style="{{ $product->image ? 'display: none;' : '' }}">
                        <img src="{{ asset('assets/img/placeholders/course-not-found.png') }}" 
                             alt="{{ $product->titre }}" 
                             class="size-60 rounded-8 object-cover">
                      </div>
                      </td>
                      <td>
                        <div class="text-14 fw-500 text-dark-1">{{ $product->titre }}</div>
                        <div class="text-12 text-light-1">{{ $product->period }}</div>
                      </td>
                      <td>
                        <span class="badge bg-light-3 text-dark-1">{{ $product->category->titre ?? 'N/A' }}</span>
                      </td>
                      <td>
                        <div class="text-14 text-dark-1">{{ $product->teacher->nom ?? 'N/A' }}</div>
                      </td>
                      <td>
                        <div class="text-14 fw-500 text-dark-1">{{ number_format($product->prix, 2) }} TND</div>
                      </td>
                      <td>
                        @if($product->statut)
                          <span class="badge bg-success">{{ __('messages.active') }}</span>
                        @else
                          <span class="badge bg-danger">{{ __('messages.inactive') }}</span>
                        @endif
                      </td>
                      <td>
                        <div class="text-14 text-dark-1">{{ $product->created_at->format('d/m/Y') }}</div>
                        <div class="text-12 text-light-1">{{ $product->created_at->format('H:i') }}</div>
                      </td>
                      <td>
                        <div class="d-flex items-center">
                          <a href="{{ route('admin.products.show', $product) }}" 
                             class="button -sm -light-1 mr-10" 
                             title="{{ __('messages.view') }}">
                            <i class="icon-eye"></i>
                          </a>
                          <a href="{{ route('admin.products.edit', $product) }}" 
                             class="button -sm -blue-1 mr-10" 
                             title="{{ __('messages.edit') }}">
                            <i class="icon-edit"></i>
                          </a>
                          <button type="button" 
                                  class="button -sm -red-1" 
                                  onclick="deleteCourse({{ $product->id }})"
                                  title="{{ __('messages.delete') }}">
                            <i class="icon-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex items-center justify-center mt-30">
              {{ $products->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function deleteCourse(productId) {
    Swal.fire({
        title: '{{ __("messages.confirm_delete") }}',
        text: '{{ __("messages.are_you_sure_delete_course") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("messages.yes_delete") }}',
        cancelButtonText: '{{ __("messages.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __("messages.deleting_course") }}',
                text: '{{ __("messages.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("messages.success") }}',
                        text: data.message,
                        confirmButtonText: '{{ __("messages.ok") }}',
                        confirmButtonColor: '#7c3aed'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: data.message,
                        confirmButtonText: '{{ __("messages.ok") }}',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: '{{ __("messages.unexpected_error_occurred") }}',
                    confirmButtonText: '{{ __("messages.ok") }}',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}
</script>
@endsection
