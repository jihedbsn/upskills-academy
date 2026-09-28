<style>
  .stats-section {
    background: #ffffff;
    position: relative;
    overflow: hidden;
  }

  .stats-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(0,0,0,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
  }

  .stats-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 36px 28px;
    text-align: center;
    transition: transform 240ms cubic-bezier(.645,.045,.355,1), box-shadow 240ms cubic-bezier(.645,.045,.355,1), border-color 150ms ease;
    border: 1px solid #dddfe2;
    position: relative;
    z-index: 1;
  }

  .stats-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(8, 23, 54, 0.10);
    border-color: #96d6d4;
  }

  .stats-card__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 20px;
    background: #eff9f9;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #297a7b;
    transition: background 150ms ease, color 150ms ease;
  }

  .stats-card:hover .stats-card__icon {
    background: #3cb4b0;
    color: #081d36;
  }

  .stats-card__number {
    font-size: 46px;
    font-weight: 700;
    color: #093162;
    line-height: 1.2;
    margin-bottom: 10px;
  }

  .stats-card__label {
    font-size: 16px;
    color: #5c626b;
    font-weight: 500;
    line-height: 1.4;
  }

  @media (max-width: 768px) {
    .stats-card {
      padding: 30px 20px;
      margin-bottom: 20px;
    }

    .stats-card__number {
      font-size: 36px;
    }

    .stats-card__label {
      font-size: 16px;
    }

    .stats-card__icon {
      width: 60px;
      height: 60px;
      font-size: 28px;
    }
  }
</style>

<section class="stats-section layout-pt-lg layout-pb-sm">
  <div class="container">
   

    <div class="row y-gap-30">
      <!-- Programmes de formation -->
      <div class="col-lg-3 col-md-6" data-anim="slide-up delay-1">
        <div class="stats-card">
          <div class="stats-card__icon">
            <i class="icon icon-play"></i>
          </div>
          <div class="stats-card__number">
            +<span class="counter-number" data-target="{{ $achievements->training_programs ?? 0 }}">0</span>
          </div>
          <div class="stats-card__label">{{ __('messages.training_programs') }}</div>
        </div>
      </div>

      <!-- Étudiants inscrits -->
      <div class="col-lg-3 col-md-6" data-anim="slide-up delay-2">
        <div class="stats-card">
          <div class="stats-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="stats-card__number">
            +<span class="counter-number" data-target="{{ $achievements->registered_students ?? 0 }}">0</span>
          </div>
          <div class="stats-card__label">{{ __('messages.registered_students') }}</div>
        </div>
      </div>

      <!-- Livres de l'académie -->
      <div class="col-lg-3 col-md-6" data-anim="slide-up delay-3">
        <div class="stats-card">
          <div class="stats-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
          </div>
          <div class="stats-card__number">
            +<span class="counter-number" data-target="{{ $achievements->academy_books ?? 0 }}">0</span>
          </div>
          <div class="stats-card__label">{{ __('messages.academy_books') }}</div>
        </div>
      </div>

      <!-- Formateurs prêts -->
      <div class="col-lg-3 col-md-6" data-anim="slide-up delay-4">
        <div class="stats-card">
          <div class="stats-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stats-card__number">
            +<span class="counter-number" data-target="{{ $achievements->ready_instructors ?? 0 }}">0</span>
          </div>
          <div class="stats-card__label">{{ __('messages.ready_instructors') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Animation des compteurs
  function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 secondes
    const step = target / (duration / 16); // 60 FPS
    let current = 0;

    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        element.textContent = target.toLocaleString();
        clearInterval(timer);
      } else {
        element.textContent = Math.floor(current).toLocaleString();
      }
    }, 16);
  }

  // Observer pour démarrer l'animation quand la section est visible
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const counters = entry.target.querySelectorAll('.counter-number');
        counters.forEach(counter => {
          // Vérifier si le compteur n'a pas encore été animé
          if (!counter.classList.contains('animated')) {
            counter.classList.add('animated');
            animateCounter(counter);
          }
        });
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  const statsSection = document.querySelector('.stats-section');
  if (statsSection) {
    observer.observe(statsSection);
  }
});
</script>

