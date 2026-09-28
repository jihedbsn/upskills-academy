<div id="discussion" class="pt-80 lg:pt-60">
  <div class="row y-gap-30">
    <div class="col-lg-12">
      <div class="section-header">
        <h2 class="text-24 fw-500 text-dark-1 mb-10">{{ __('messages.discussion') }}</h2>
        <p class="text-15 text-light-1">{{ __('messages.discussion_description') }}</p>
      </div>
    </div>
  </div>

  @php
    $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());
    // $discussions est maintenant passé depuis le contrôleur
  @endphp

  <!-- Affichage des discussions -->
  <div id="discussions-container" class="mt-40">
    @if($discussions->count() > 0)
      <div class="row y-gap-30">
        @foreach($discussions as $discussion)
          <div class="col-12">
                         <div class="discussion-card bg-white rounded-16 p-30 shadow-sm">
              <!-- Commentaire étudiant -->
              <div class="d-flex items-start">
                                 @if($discussion->student->image)
                   <div class="size-50 rounded-full mr-20 overflow-hidden">
                     <img src="{{ asset('uploads/students/' . $discussion->student->image) }}" 
                          alt="{{ $discussion->student->full_name }}" 
                          class="w-100 h-100 object-cover">
                   </div>
                 @else
                   <div class="size-50 rounded-full bg-gradient-purple d-flex justify-center items-center mr-20">
                     <div class="text-16 text-white fw-600">{{ substr($discussion->student->full_name, 0, 1) }}</div>
                   </div>
                 @endif
                                 <div class="flex-1">
                   <div class="d-flex items-center justify-content-between mb-15">
                     <div class="d-flex items-center">
                       <h5 class="text-18 fw-600 text-dark-1">{{ $discussion->student->full_name }}</h5>
                     </div>
                     <div class="text-14 text-light-1">{{ $discussion->created_at->format('d/m/Y H:i') }}</div>
                   </div>
                  <div class="text-16 text-dark-1 leading-relaxed mb-20">
                    {{ $discussion->commentaire }}
                  </div>
                </div>
              </div>

              <!-- Réponses admin -->
              @if($discussion->responses->count() > 0)
                <div class="admin-responses mt-25 pt-25 border-top border-light-3">
                  @foreach($discussion->responses as $response)
                    <div class="admin-response bg-light-4 rounded-12 p-20 mb-15">
                      <div class="d-flex items-start">
                        <div class="size-40 rounded-full bg-gradient-orange d-flex justify-center items-center mr-15">
                          <div class="text-14 text-white fw-600">A</div>
                        </div>
                        <div class="flex-1">
                          <div class="d-flex items-center justify-content-between mb-10">
                            <h6 class="text-16 fw-600 text-dark-1">{{ __('messages.admin') }}</h6>
                            <div class="text-13 text-light-1">{{ $response->created_at->format('d/m/Y H:i') }}</div>
                          </div>
                          <div class="text-15 text-dark-1 leading-relaxed">
                            {{ $response->reponse }}
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div id="no-discussions" class="text-center py-60">
        <div class="empty-state">
          <div class="icon-chat text-64 text-light-2 mb-20"></div>
          <h5 class="text-20 fw-500 text-dark-1 mb-15">{{ __('messages.no_discussions_yet') }}</h5>
          <p class="text-16 text-light-1 mb-0">{{ __('messages.be_first_to_comment') }}</p>
        </div>
      </div>
    @endif
  </div>

  <!-- Formulaire d'ajout de commentaire -->
  <div class="mt-60">
    @if(auth()->check())
      @if($hasPurchased)
        <div class="comment-form-section">
          <div class="row">
            <div class="col-lg-8">
                             <div class="comment-form-card bg-white rounded-16 p-30 shadow-sm">
                                 <div class="d-flex items-center mb-25">
                   <h4 class="text-20 fw-600 text-dark-1 mb-0">{{ __('messages.add_comment') }}</h4>
                 </div>
                
                <form id="discussionForm" class="discussion-form">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                  <div class="form-group mb-20">
                                         <textarea 
                       name="commentaire" 
                       id="commentaire"
                       rows="4" 
                       class="form-control" 
                       placeholder="{{ __('messages.write_your_comment') }}"
                       required
                     ></textarea>
                    <div id="commentaire-error" class="invalid-feedback" style="display: none;"></div>
                  </div>
                  <div class="d-flex items-center justify-content-between">
                    <div class="text-13 text-light-1">
                      {{ __('messages.comment_guidelines') }}
                    </div>
                    <button type="submit" id="submitComment" class="button -md -purple-1 text-white">
                      <span id="submitText">{{ __('messages.post_comment') }}</span>
                      <span id="submitLoading" style="display: none;">
                        <div class="spinner-border spinner-border-sm" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      @else
        <div class="purchase-required-card bg-gradient-light rounded-16 p-40 text-center border">
          <div class="icon-lock text-48 text-light-1 mb-20"></div>
          <h5 class="text-20 fw-600 text-dark-1 mb-15">{{ __('messages.purchase_required') }}</h5>
          <p class="text-16 text-light-1 mb-25">{{ __('messages.purchase_to_comment') }}</p>
          <a href="{{ route('checkout.index') }}" class="button -md -purple-1 text-white">
            {{ __('messages.purchase_course') }}
          </a>
        </div>
      @endif
    @else
      <div class="login-required-card bg-gradient-light rounded-16 p-40 text-center border">
        <div class="icon-user text-48 text-light-1 mb-20"></div>
        <h5 class="text-20 fw-600 text-dark-1 mb-15">{{ __('messages.login_required') }}</h5>
        <p class="text-16 text-light-1 mb-25">{{ __('messages.login_to_comment') }}</p>
        <a href="{{ route('login') }}" class="button -md -purple-1 text-white">
          {{ __('messages.login') }}
        </a>
      </div>
    @endif
  </div>
</div>

<style>
/* Discussion Cards */
.discussion-card {
  background: #ffffff;
}

/* Avatar Styles */
.bg-gradient-purple {
  background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
}

.bg-gradient-orange {
  background: linear-gradient(135deg, #fd7e14 0%, #e67e22 100%);
}

/* Admin Responses */
.admin-responses {
  background: #f8f9fa;
  margin: 0 -30px -30px -30px;
  padding: 25px 30px 30px 30px;
  border-radius: 0 0 16px 16px;
}

.admin-response {
  background: #ffffff;
}

/* Empty State */
.empty-state {
  padding: 40px 20px;
}

/* Form Styles */
.discussion-form .form-control {
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.discussion-form .form-control:focus {
  border-color: #6f42c1;
  box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
  outline: none;
}

.discussion-form .form-control.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Cards */
.comment-form-card,
.purchase-required-card,
.login-required-card {
  background: #ffffff;
}

.bg-gradient-light {
  background: #f8f9fa;
}

/* Badge Styles */
.badge {
  border-radius: 20px !important;
  font-weight: 600 !important;
  letter-spacing: 0.5px !important;
}

.bg-success {
  background: #28a745 !important;
}

/* Error Styles */
.invalid-feedback {
  display: block !important;
  color: #dc3545 !important;
  font-size: 13px !important;
  margin-top: 8px !important;
  font-weight: 500 !important;
}

/* Responsive */
@media (max-width: 768px) {
  .discussion-card {
    padding: 20px !important;
  }
  
  .admin-responses {
    margin: 0 -20px -20px -20px !important;
    padding: 20px !important;
  }
  
  .comment-form-card {
    padding: 20px !important;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Gestion du formulaire de discussion avec AJAX
  const discussionForm = document.getElementById('discussionForm');
  const commentaireTextarea = document.getElementById('commentaire');
  const submitButton = document.getElementById('submitComment');
  const submitText = document.getElementById('submitText');
  const submitLoading = document.getElementById('submitLoading');
  const commentaireError = document.getElementById('commentaire-error');
  const discussionsContainer = document.getElementById('discussions-container');
  const noDiscussions = document.getElementById('no-discussions');

  if (discussionForm) {
    discussionForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validation côté client
      const commentaire = commentaireTextarea.value.trim();
      if (!commentaire) {
        showError('{{ __("messages.please_enter_comment") }}');
        return false;
      }

      // Désactiver le bouton et afficher le loading
      setLoadingState(true);

      // Préparer les données
      const formData = new FormData();
      formData.append('_token', '{{ csrf_token() }}');
      formData.append('product_id', '{{ $product->id }}');
      formData.append('commentaire', commentaire);

      // Envoyer la requête AJAX
      fetch('{{ route("discussions.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
             .then(data => {
         if (data.success) {
           // Succès - ajouter le commentaire à la liste et vider le formulaire
           addDiscussionToList(data.discussion);
           commentaireTextarea.value = '';
           hideError();
           showSuccess('{{ __("messages.comment_submitted_successfully") }}');
         } else {
           // Erreur
           showError(data.message || '{{ __("messages.error_submitting_comment") }}');
         }
       })
      .catch(error => {
        console.error('Error:', error);
        showError('{{ __("messages.error_submitting_comment") }}');
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

  function showError(message) {
    commentaireError.textContent = message;
    commentaireError.style.display = 'block';
    commentaireTextarea.classList.add('is-invalid');
  }

  function hideError() {
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
     discussionForm.parentNode.insertBefore(successDiv, discussionForm);
     
     // Supprimer automatiquement après 3 secondes
     setTimeout(() => {
       if (successDiv.parentNode) {
         successDiv.remove();
       }
     }, 3000);
   }

   function addDiscussionToList(discussion) {
     // Supprimer le message "aucune discussion" s'il existe
     if (noDiscussions) {
       noDiscussions.remove();
     }

     // Sécuriser les données
     const studentName = discussion.student_name || 'Utilisateur';
     const studentInitial = studentName.charAt(0) || 'U';
     const studentImage = discussion.student_image || null;

     // Créer l'avatar (image ou initiale)
     let avatarHtml;
           if (studentImage) {
        avatarHtml = `
          <div class="size-50 rounded-full mr-20 overflow-hidden">
            <img src="/uploads/students/${studentImage}" alt="${studentName}" class="w-100 h-100 object-cover">
          </div>
        `;
      } else {
        avatarHtml = `
          <div class="size-50 rounded-full bg-gradient-purple d-flex justify-center items-center mr-20">
            <div class="text-16 text-white fw-600">${studentInitial}</div>
          </div>
        `;
      }

     // Créer le HTML pour la nouvelle discussion
     const discussionHtml = `
               <div class="col-12">
          <div class="discussion-card bg-white rounded-16 p-30 shadow-sm" data-id="${discussion.id}">
           <div class="d-flex items-start">
             ${avatarHtml}
             <div class="flex-1">
               <div class="d-flex items-center justify-content-between mb-15">
                                   <div class="d-flex items-center">
                    <h5 class="text-18 fw-600 text-dark-1">${studentName}</h5>
                  </div>
                 <div class="text-14 text-light-1">${discussion.created_at}</div>
               </div>
               <div class="text-16 text-dark-1 leading-relaxed mb-20">
                 ${discussion.commentaire}
               </div>
             </div>
           </div>
         </div>
       </div>
     `;

     // Ajouter au début de la liste
     discussionsContainer.insertAdjacentHTML('afterbegin', discussionHtml);
   }

 });
</script>
