<!-- Simple Language Switcher -->
@php
  $uniqueId = 'languageDropdown' . uniqid();
@endphp
<div class="language-switcher" data-dropdown-id="{{ $uniqueId }}">
  <div class="dropdown js-dropdown js-language-active">
    <div class="dropdown__button d-flex items-center text-13 rounded-8 px-12 py-8 lh-12" onclick="toggleLanguageDropdownNew(event, this)" style="cursor: pointer;">
      <span class="js-dropdown-title">
        @if(app()->getLocale() == 'ar')
          AR
        @else
          EN
        @endif
      </span>
      <i class="icon text-8 ml-8 icon-chevron-down"></i>
    </div>

    <div class="toggle-element -dropdown -dark-bg-dark-2 -dark-border-white-10 js-language-toggle" id="{{ $uniqueId }}">
      <div class="text-14 y-gap-15">
        @php
            $currentRouteName = Route::currentRouteName();
            $currentParams = Route::current() ? Route::current()->parameters() : [];
        @endphp
        <div><a href="{{ $currentRouteName ? route($currentRouteName, array_merge($currentParams, ['locale' => 'en'])) : url('en') }}" class="d-block js-dropdown-link {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a></div>
        <div><a href="{{ $currentRouteName ? route($currentRouteName, array_merge($currentParams, ['locale' => 'ar'])) : url('ar') }}" class="d-block js-dropdown-link {{ app()->getLocale() == 'ar' ? 'active' : '' }}">العربية</a></div>
      </div>
    </div>
  </div>
</div>

<style>
/* Style pour le sélecteur de langue basé sur le template */
.language-switcher {
  position: relative;
  display: inline-block;
}

.js-language-active .dropdown__button {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  transition: all 0.3s ease;
  cursor: pointer;
}

.js-language-active .dropdown__button:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.js-language-active .dropdown__button .js-dropdown-title {
  font-weight: 500;
  min-width: 30px;
  text-align: center;
}

.js-language-active .toggle-element {
  background: white !important;
  border: 1px solid #e5e5e5 !important;
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
  margin-top: 5px !important;
  min-width: 120px !important;
  display: none !important;
  position: absolute !important;
  z-index: 999999 !important;
  right: 0 !important;
  top: 100% !important;
  padding: 5px !important;
  pointer-events: auto !important;
}

.js-language-active .toggle-element.show {
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  pointer-events: auto !important;
}

.js-language-active .js-dropdown-link {
  padding: 8px 15px !important;
  color: #333 !important;
  text-decoration: none !important;
  transition: background-color 0.2s !important;
  border-radius: 4px !important;
  margin: 2px 5px !important;
  display: block !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: relative !important;
  z-index: 1000000 !important;
}

.js-language-active .js-dropdown-link:hover {
  background-color: #f8f9fa !important;
  text-decoration: none !important;
}

.js-language-active .js-dropdown-link.active {
  background-color: #e9ecef !important;
  color: #495057 !important;
  font-weight: 500 !important;
}

/* RTL Support */
[dir="rtl"] .js-language-active .dropdown__button .icon {
  margin-left: 0;
  margin-right: 40px;
}

[dir="rtl"] .js-language-active .js-dropdown-link {
  text-align: right;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .js-language-active .dropdown__button {
    padding: 6px 12px;
    font-size: 12px;
  }

  .js-language-active .dropdown__button .js-dropdown-title {
    min-width: 25px;
  }

  .js-language-active .toggle-element {
    min-width: 100px;
  }

  .js-dropdown-link {
    padding: 6px 12px;
    font-size: 12px;
  }
}

/* Mobile menu language switcher specific styles */
.mobile-footer .language-switcher {
  position: relative;
  z-index: 9999999;
}

.mobile-footer .js-language-active .toggle-element {
  position: fixed !important;
  z-index: 9999999 !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  right: auto !important;
  bottom: 80px !important;
  top: auto !important;
}

.mobile-footer .js-language-active .dropdown__button {
  background: white !important;
  border: 1px solid #e5e5e5 !important;
  color: #333 !important;
  width: 100%;
  justify-content: center;
}
</style>

<script>
// New function to toggle dropdown with unique ID
function toggleLanguageDropdownNew(event, element) {
  console.log('toggleLanguageDropdownNew called');

  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  // Find the parent language-switcher
  const languageSwitcher = element.closest('.language-switcher');
  console.log('languageSwitcher:', languageSwitcher);

  if (!languageSwitcher) {
    console.error('Language switcher not found');
    return;
  }

  // Get the unique dropdown ID
  const dropdownId = languageSwitcher.getAttribute('data-dropdown-id');
  console.log('dropdownId:', dropdownId);

  const dropdown = document.getElementById(dropdownId);
  console.log('dropdown element:', dropdown);

  if (!dropdown) {
    console.error('Dropdown not found:', dropdownId);
    return;
  }

  const isVisible = dropdown.classList.contains('show');
  console.log('isVisible:', isVisible);

  // Close all other language dropdowns first
  document.querySelectorAll('.language-switcher .toggle-element.show').forEach(function(el) {
    if (el.id !== dropdownId) {
      el.classList.remove('show');
      el.style.display = 'none';
    }
  });

  // Toggle the current dropdown
  if (!isVisible) {
    console.log('Opening dropdown');
    dropdown.classList.add('show');
    dropdown.style.display = 'block';
    dropdown.style.visibility = 'visible';
    dropdown.style.opacity = '1';
    dropdown.style.pointerEvents = 'auto';
  } else {
    console.log('Closing dropdown');
    dropdown.classList.remove('show');
    dropdown.style.display = 'none';
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
  const languageSwitcher = e.target.closest('.language-switcher');

  if (!languageSwitcher) {
    document.querySelectorAll('.language-switcher .toggle-element.show').forEach(function(dropdown) {
      dropdown.classList.remove('show');
      dropdown.style.display = 'none';
    });
  }
});
</script>
