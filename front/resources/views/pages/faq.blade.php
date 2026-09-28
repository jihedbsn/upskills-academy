<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  @include('layouts.head')
  <title>{{ $page->{'meta_title_' . app()->getLocale()} ?? $page->{'titre_' . app()->getLocale()} }} - Upskills Academy</title>
  <meta name="description" content="{{ $page->{'description_' . app()->getLocale()} }}">

  <style>
    .faq-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .faq-item {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      margin-bottom: 16px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .faq-item:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .faq-question {
      background: #f8f9fa;
      padding: 20px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: none;
      width: 100%;
      text-align: left;
      font-size: 16px;
      font-weight: 600;
      color: #333;
      transition: all 0.3s ease;
    }

    .faq-question:hover {
      background: #e9ecef;
    }

    .faq-question.active {
      background: #0071ae;
      color: white;
    }

    .faq-icon {
      font-size: 18px;
      transition: transform 0.3s ease;
      color: #0071ae;
    }

    .faq-question.active .faq-icon {
      transform: rotate(45deg);
      color: white;
    }

    .faq-answer {
      padding: 0 20px;
      max-height: 0;
      overflow: hidden;
      transition: all 0.3s ease;
      background: white;
    }

    .faq-answer.active {
      padding: 20px;
      max-height: 1000px;
    }

    .faq-answer-content {
      color: #555;
      line-height: 1.6;
      font-size: 15px;
    }

    .faq-answer-content ul,
    .faq-answer-content ol {
      margin: 10px 0;
      padding-left: 20px;
    }

    .faq-answer-content li {
      margin-bottom: 5px;
    }

    .faq-search {
      margin-bottom: 30px;
    }

    .faq-search input {
      width: 100%;
      padding: 12px 20px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    .faq-search input:focus {
      outline: none;
      border-color: #0071ae;
    }

    .no-results {
      text-align: center;
      padding: 40px;
      color: #666;
      font-style: italic;
    }

    @media (max-width: 768px) {
      .faq-question {
        padding: 16px;
        font-size: 15px;
      }

      .faq-answer.active {
        padding: 16px;
      }
    }
  </style>
</head>

<body class="preloader-visible" data-barba="wrapper">
  <!-- preloader start -->
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>
  <!-- preloader end -->

  <main class="main-content">
    @include('layouts.header')

    <div class="content-wrapper js-content-wrapper">
      <!-- Breadcrumbs -->
      <section class="layout-pt-sm layout-pb-sm bg-light-6">
        <div class="container">
          <div class="row y-gap-30 justify-between items-center">
            <div class="col-auto">
              <div class="d-flex items-center h-100 fw-500">
                <div class="breadcrumbs__content">
                  <div class="breadcrumbs__item">
                    <a href="{{ route('accueil.index') }}">{{ __('messages.home') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <a href="{{ route('pages.index') }}">{{ __('messages.pages') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <span class="us-crumb-current">{{ $page->{'titre_' . app()->getLocale()} ?? $page->titre_en }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Content -->
      <section class="layout-pt-md layout-pb-md">
        <div class="container">
          <div class="row justify-center">
            <div class="col-xl-10 col-lg-11">
              <!-- FAQ Header -->
              <div class="text-center mb-50">
                <h1 class="text-40 lh-14 mb-20">{{ $page->{'titre_' . app()->getLocale()} }}</h1>
                <p class="text-16 text-light-1">{{ __('messages.faq_subtitle') ?? 'Find answers to frequently asked questions' }}</p>
              </div>

              <!-- FAQ Search -->
              <div class="faq-search">
                <input type="text" id="faqSearch" placeholder="{{ __('messages.search_faq') ?? 'Search FAQ...' }}">
              </div>

              <!-- FAQ Items -->
              <div class="faq-container" id="faqContainer">
                @forelse($faqItems as $index => $item)
                  <div class="faq-item" data-question="{{ strtolower($item['question']) }}" data-answer="{{ strtolower($item['answer']) }}">
                    <button class="faq-question" onclick="toggleFaq({{ $index }})">
                      <span>{{ $item['question'] }}</span>
                      <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer" id="faq-answer-{{ $index }}">
                      <div class="faq-answer-content">
                        {!! $item['answer'] !!}
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="no-results">
                    <p>{{ __('messages.no_faq_items') ?? 'No FAQ items found.' }}</p>
                  </div>
                @endforelse
              </div>

              <!-- Back to Pages -->
              <div class="text-center mt-50">
                <a href="{{ route('pages.index') }}" class="button -md -outline-dark-1 text-dark-1">
                  <i class="icon-arrow-left mr-10"></i>
                  {{ __('messages.back_to_pages') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <!-- Scripts -->
  <script src="{{ asset('assets/js/vendors.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script>
    // FAQ Toggle Function
    function toggleFaq(index) {
      const question = document.querySelector(`[onclick="toggleFaq(${index})"]`);
      const answer = document.getElementById(`faq-answer-${index}`);
      const icon = question.querySelector('.faq-icon');

      // Close all other FAQ items
      document.querySelectorAll('.faq-question').forEach(q => {
        if (q !== question) {
          q.classList.remove('active');
          const answerId = q.getAttribute('onclick').match(/\d+/)[0];
          document.getElementById(`faq-answer-${answerId}`).classList.remove('active');
          q.querySelector('.faq-icon').textContent = '+';
        }
      });

      // Toggle current FAQ item
      if (question.classList.contains('active')) {
        question.classList.remove('active');
        answer.classList.remove('active');
        icon.textContent = '+';
      } else {
        question.classList.add('active');
        answer.classList.add('active');
        icon.textContent = '−';
      }
    }

    // FAQ Search Function
    document.getElementById('faqSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const faqItems = document.querySelectorAll('.faq-item');

      faqItems.forEach(item => {
        const question = item.getAttribute('data-question');
        const answer = item.getAttribute('data-answer');

        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });

      // Show no results message if no items match
      const visibleItems = document.querySelectorAll('.faq-item[style="display: block;"], .faq-item:not([style*="display: none"])');
      const container = document.getElementById('faqContainer');

      if (visibleItems.length === 0 && searchTerm !== '') {
        if (!document.querySelector('.no-search-results')) {
          const noResults = document.createElement('div');
          noResults.className = 'no-search-results no-results';
          noResults.innerHTML = '<p>{{ __("messages.no_search_results") ?? "No results found for your search." }}</p>';
          container.appendChild(noResults);
        }
      } else {
        const noResults = document.querySelector('.no-search-results');
        if (noResults) {
          noResults.remove();
        }
      }
    });

    // Allow opening multiple FAQ items with Ctrl/Cmd + click
    document.addEventListener('click', function(e) {
      if (e.ctrlKey || e.metaKey) {
        const question = e.target.closest('.faq-question');
        if (question) {
          e.preventDefault();
          const index = question.getAttribute('onclick').match(/\d+/)[0];
          const answer = document.getElementById(`faq-answer-${index}`);
          const icon = question.querySelector('.faq-icon');

          if (question.classList.contains('active')) {
            question.classList.remove('active');
            answer.classList.remove('active');
            icon.textContent = '+';
          } else {
            question.classList.add('active');
            answer.classList.add('active');
            icon.textContent = '−';
          }
        }
      }
    });
  </script>
</body>

</html>
