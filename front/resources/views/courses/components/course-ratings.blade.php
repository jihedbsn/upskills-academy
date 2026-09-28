<div id="course-ratings" class="pt-60 lg:pt-40">
  <h2 class="text-20 fw-500 mb-30">{{ __('messages.course_ratings') }}</h2>

  @php
    $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());
    $hasCompleted = auth()->check() && $product->hasStudentCompleted(auth()->id());
    $canRate = auth()->check() && \App\Models\Student::find(auth()->id())->canRateProduct($product->id);
    $hasRated = auth()->check() && $product->hasStudentRated(auth()->id());
    $approvedRatingsQuery = $product->getApprovedRatings();
    $approvedRatings = $approvedRatingsQuery->orderBy('created_at', 'desc')->get();
    $ratings = $approvedRatings; // Already ordered
    $averageRating = $approvedRatings->avg('rating') ?? 0;
    $totalRatings = $approvedRatings->count();
  @endphp

  <!-- Statistiques des ratings -->
  <div class="bg-light-4 rounded-12 p-30 mb-40">
    <div class="row y-gap-30">
      <div class="col-lg-4">
        <div class="text-center">
          <div class="text-48 fw-600 text-dark-1 mb-10">{{ number_format($averageRating, 1) }}</div>
          <div class="d-flex justify-center x-gap-5 mb-10">
            @for($i = 1; $i <= 5; $i++)
              <div class="icon-star text-16 {{ $i <= $averageRating ? 'text-yellow-1' : 'text-light-3' }}"></div>
            @endfor
          </div>
          <div class="text-14 text-light-1">{{ $totalRatings }} {{ __('messages.ratings') }}</div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="y-gap-10">
          @for($star = 5; $star >= 1; $star--)
            @php
              $starCount = $approvedRatings->where('rating', $star)->count();
              $percentage = $totalRatings > 0 ? ($starCount / $totalRatings) * 100 : 0;
            @endphp
            <div class="d-flex items-center">
              <div class="text-14 fw-500 text-dark-1" style="width: 20px;">{{ $star }}</div>
              <div class="icon-star text-12 text-yellow-1 ml-10"></div>
              <div class="flex-1 bg-light-3 rounded-4 ml-10" style="height: 8px;">
                <div class="bg-yellow-1 rounded-4" style="width: {{ $percentage }}%; height: 100%;"></div>
              </div>
              <div class="text-14 text-light-1 ml-10" style="width: 40px;">{{ $starCount }}</div>
            </div>
          @endfor
        </div>
      </div>
    </div>
  </div>

  <!-- Affichage des ratings -->
  @if($ratings->count() > 0)
    <div class="y-gap-30">
      @foreach($ratings as $rating)
        <div class="bg-light-4 rounded-12 p-30">
          <div class="d-flex items-start">
                         @if($rating->student && $rating->student->image)
               <div class="size-40 rounded-full mr-15 overflow-hidden">
                 <img src="{{ asset('uploads/students/' . $rating->student->image) }}"
                      alt="{{ $rating->student->full_name }}"
                      class="w-100 h-100 object-cover">
               </div>
             @else
               <div class="size-40 rounded-full bg-gradient-purple d-flex justify-center items-center mr-15">
                 <div class="text-14 text-white fw-600">{{ $rating->student ? substr($rating->student->full_name, 0, 1) : 'U' }}</div>
               </div>
             @endif
            <div class="flex-1">
              <div class="d-flex items-center justify-between mb-10">
                <div class="d-flex items-center">
                  <h5 class="text-16 fw-500 text-dark-1">{{ $rating->student ? $rating->student->full_name : 'Utilisateur' }}</h5>
                  <div class="d-flex x-gap-5 items-center ml-15">
                    @for($i = 1; $i <= 5; $i++)
                      <div class="icon-star text-12 {{ $i <= $rating->rating ? 'text-yellow-1' : 'text-light-3' }}"></div>
                    @endfor
                  </div>
                </div>
                <div class="text-14 text-light-1">{{ $rating->created_at->format('d/m/Y') }}</div>
              </div>
              @if($rating->commentaire)
                <div class="text-15 text-dark-1 leading-relaxed">
                  {{ $rating->commentaire }}
                </div>
              @endif

              {{-- Admin Response --}}
              @if($rating->admin_response)
                <div class="mt-20 ps-20 border-start border-3 border-primary">
                  <div class="d-flex align-items-center mb-10">
                    <div class="size-30 rounded-full bg-primary d-flex justify-center items-center me-10">
                      <i class="icon-user text-12 text-white"></i>
                    </div>
                    <div>
                      <span class="text-14 fw-600 text-primary">{{ __('messages.admin_response') }}</span>
                      @if($rating->admin_response_at)
                        <span class="text-12 text-light-1 ms-10">{{ $rating->admin_response_at->format('d/m/Y') }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="text-14 text-dark-1 leading-relaxed bg-white rounded-8 p-15">
                    {{ $rating->admin_response }}
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="bg-light-4 rounded-12 p-40 text-center">
      <div class="icon-star text-48 text-light-1 mb-20"></div>
      <h5 class="text-18 fw-500 text-dark-1 mb-10">{{ __('messages.no_ratings_yet') }}</h5>
      <p class="text-14 text-light-1">{{ __('messages.be_first_to_rate') }}</p>
    </div>
  @endif



  <!-- Formulaire d'ajout de rating -->
  @if(auth()->check())
    @if($canRate && !$hasRated)
      <div class="mt-40">
        <h4 class="text-18 fw-500 mb-20">{{ __('messages.rate_this_course') }}</h4>
                 <form id="ratingForm" class="rating-form">
           @csrf
           <input type="hidden" name="product_id" value="{{ $product->id }}">

           <!-- Système d'étoiles -->
           <div class="form-group mb-20">
             <label class="text-16 fw-500 text-dark-1 mb-10">{{ __('messages.your_rating') }}</label>
             <div class="star-rating">
               @for($i = 5; $i >= 1; $i--)
                 <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="star-input" required>
                 <label for="star{{ $i }}" class="star-label">
                   <div class="icon-star text-24 text-light-3"></div>
                 </label>
               @endfor
             </div>
             <div id="rating-error" class="text-12 text-red-1 mt-5" style="display: none;"></div>
           </div>

           <!-- Commentaire -->
           <div class="form-group mb-20">
             <label class="text-16 fw-500 text-dark-1 mb-10">{{ __('messages.your_review') }} ({{ __('messages.optional') }})</label>
             <textarea
               name="commentaire"
               id="rating-commentaire"
               rows="4"
               class="form-control"
               placeholder="{{ __('messages.write_your_review') }}"
             ></textarea>
             <div id="commentaire-error" class="invalid-feedback" style="display: none;"></div>
           </div>

           <div class="mt-20">
             <button type="submit" id="submitRating" class="button -md -purple-1 text-white">
               <span id="submitText">{{ __('messages.submit_rating') }}</span>
               <span id="submitLoading" style="display: none;">
                 <div class="spinner-border spinner-border-sm" role="status">
                   <span class="visually-hidden">Loading...</span>
                 </div>
               </span>
             </button>
           </div>
         </form>
      </div>
         @elseif($hasRated)
       <div class="mt-40 bg-light-4 rounded-12 p-30 text-center">
         <div class="icon-check-circle text-32 text-light-1 mb-15"></div>
         <h5 class="text-16 fw-500 text-dark-1 mb-10">{{ __('messages.already_rated') }}</h5>
         <p class="text-14 text-light-1">{{ __('messages.one_rating_per_course') }}</p>
       </div>
    @elseif(!$hasPurchased)
      <div class="mt-40 bg-light-4 rounded-12 p-30 text-center">
        <div class="icon-lock text-32 text-light-1 mb-15"></div>
        <h5 class="text-16 fw-500 text-dark-1 mb-10">{{ __('messages.purchase_required') }}</h5>
        <p class="text-14 text-light-1 mb-20">{{ __('messages.purchase_to_rate') }}</p>
        <a href="{{ route('checkout.index') }}" class="button -md -purple-1 text-white">
          {{ __('messages.purchase_course') }}
        </a>
      </div>
    @endif
  @else
    <div class="mt-40 bg-light-4 rounded-12 p-30 text-center">
      <div class="icon-user text-32 text-light-1 mb-15"></div>
      <h5 class="text-16 fw-500 text-dark-1 mb-10">{{ __('messages.login_required') }}</h5>
      <p class="text-14 text-light-1 mb-20">{{ __('messages.login_to_rate') }}</p>
      <a href="{{ route('login') }}" class="button -md -purple-1 text-white">
        {{ __('messages.login') }}
      </a>
    </div>
  @endif
</div>

<style>
.star-rating {
  display: flex;
  flex-direction: row-reverse;
  gap: 5px;
}

.star-input {
  display: none;
}

.star-label {
  cursor: pointer;
  transition: all 0.2s ease;
}

.star-label:hover,
.star-label:hover ~ .star-label,
.star-input:checked ~ .star-label {
  color: #ffc107;
}

.star-label:hover .icon-star,
.star-label:hover ~ .star-label .icon-star,
.star-input:checked ~ .star-label .icon-star {
  color: #ffc107 !important;
}

.rating-form .form-control {
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.rating-form .form-control:focus {
  border-color: #6f42c1;
  box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.rating-form .form-control.is-invalid {
  border-color: #dc3545;
}

.rating-form .invalid-feedback {
  display: block;
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Gestion du formulaire de rating avec AJAX
  const ratingForm = document.getElementById('ratingForm');
  const ratingInputs = document.querySelectorAll('input[name="rating"]');
  const commentaireTextarea = document.getElementById('rating-commentaire');
  const submitButton = document.getElementById('submitRating');
  const submitText = document.getElementById('submitText');
  const submitLoading = document.getElementById('submitLoading');
  const ratingError = document.getElementById('rating-error');
  const commentaireError = document.getElementById('commentaire-error');

  if (ratingForm) {
    ratingForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Validation côté client
      const selectedRating = document.querySelector('input[name="rating"]:checked');
      if (!selectedRating) {
        showRatingError('{{ __("messages.please_select_rating") }}');
        return false;
      }

      // Désactiver le bouton et afficher le loading
      setLoadingState(true);

      // Préparer les données
      const formData = new FormData();
      formData.append('_token', '{{ csrf_token() }}');
      formData.append('product_id', '{{ $product->id }}');
      formData.append('rating', selectedRating.value);

      console.log('Commentaire textarea element:', commentaireTextarea);
      console.log('Commentaire raw value:', commentaireTextarea.value);
      console.log('Commentaire value length:', commentaireTextarea.value.length);

      const commentValue = commentaireTextarea.value.trim();
      console.log('Commentaire trimmed value:', commentValue);
      console.log('Commentaire trimmed length:', commentValue.length);

      formData.append('commentaire', commentValue || '');

      console.log('Submitting rating with comment:', commentValue);

      // Envoyer la requête AJAX
      fetch('{{ route("ratings.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
             .then(data => {
         if (data.success) {
           // Succès - vider le formulaire et afficher le message
           ratingForm.reset();
           hideErrors();
           showSuccess('{{ __("messages.rating_submitted_successfully") }}');

           // Ajouter le nouveau rating à la liste
           addRatingToList(data.rating);
         } else {
           // Erreur
           showRatingError(data.message || '{{ __("messages.error_submitting_rating") }}');
         }
       })
      .catch(error => {
        console.error('Error:', error);
        showRatingError('{{ __("messages.error_submitting_rating") }}');
      })
      .finally(() => {
        setLoadingState(false);
      });
    });
  }

  // Fonctions utilitaires
  function setLoadingState(loading) {
    submitButton.disabled = loading;
    submitText.style.display = loading ? 'none' : 'inline';
    submitLoading.style.display = loading ? 'inline' : 'none';
  }

  function showRatingError(message) {
    ratingError.textContent = message;
    ratingError.style.display = 'block';
  }

  function showCommentaireError(message) {
    commentaireError.textContent = message;
    commentaireError.style.display = 'block';
    commentaireTextarea.classList.add('is-invalid');
  }

  function hideErrors() {
    ratingError.style.display = 'none';
    commentaireError.style.display = 'none';
    commentaireTextarea.classList.remove('is-invalid');
  }

     function showSuccess(message) {
     // Créer une notification de succès temporaire
     const successDiv = document.createElement('div');
     successDiv.className = 'alert alert-success alert-dismissible fade show';
     successDiv.innerHTML = `
       ${message}
       <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
     `;

     // Insérer avant le formulaire
     ratingForm.parentNode.insertBefore(successDiv, ratingForm);

     // Supprimer automatiquement après 3 secondes
     setTimeout(() => {
       if (successDiv.parentNode) {
         successDiv.remove();
       }
     }, 3000);
   }

   function addRatingToList(ratingData) {
     const ratingsContainer = document.querySelector('#course-ratings .y-gap-30');
     if (!ratingsContainer) return;

     // Créer l'élément HTML pour le nouveau rating
     const ratingElement = document.createElement('div');
     ratingElement.className = 'bg-light-4 rounded-12 p-30';

     // Générer les étoiles
     let starsHtml = '';
     for (let i = 1; i <= 5; i++) {
       const starClass = i <= ratingData.rating ? 'text-yellow-1' : 'text-light-3';
       starsHtml += `<div class="icon-star text-12 ${starClass}"></div>`;
     }

           // Générer l'initiale ou l'image
      const studentInitial = ratingData.student_name ? ratingData.student_name.charAt(0) : 'U';
      const avatarHtml = ratingData.student_image
        ? `<div class="size-40 rounded-full mr-15 overflow-hidden">
             <img src="/uploads/students/${ratingData.student_image}"
                  alt="${ratingData.student_name}"
                  class="w-100 h-100 object-cover">
           </div>`
        : `<div class="size-40 rounded-full bg-gradient-purple d-flex justify-center items-center mr-15">
             <div class="text-14 text-white fw-600">${studentInitial}</div>
           </div>`;

      // Badge "En attente" si non approuvé
      const pendingBadge = !ratingData.is_approved
        ? '<div class="badge bg-warning text-dark ml-10 px-8 py-2 text-10">En attente</div>'
        : '';

      ratingElement.innerHTML = `
        <div class="d-flex items-start">
          ${avatarHtml}
         <div class="flex-1">
           <div class="d-flex items-center justify-between mb-10">
             <div class="d-flex items-center">
               <h5 class="text-16 fw-500 text-dark-1">${ratingData.student_name || 'Utilisateur'}</h5>
               <div class="d-flex x-gap-5 items-center ml-15">
                 ${starsHtml}
               </div>
               ${pendingBadge}
             </div>
             <div class="text-14 text-light-1">${ratingData.created_at}</div>
           </div>
           ${ratingData.commentaire ? `<div class="text-15 text-dark-1 leading-relaxed">${ratingData.commentaire}</div>` : ''}
         </div>
       </div>
     `;

     // Ajouter au début de la liste
     ratingsContainer.insertBefore(ratingElement, ratingsContainer.firstChild);
   }

  // Animation des étoiles
  const starLabels = document.querySelectorAll('.star-label');
  starLabels.forEach(label => {
    label.addEventListener('mouseenter', function() {
      this.style.transform = 'scale(1.1)';
    });

    label.addEventListener('mouseleave', function() {
      this.style.transform = 'scale(1)';
    });
  });
});
</script>
