<!-- Currency Switcher with Search -->
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $currencies = $currencyService->getActiveCurrencies();
    $selectedCurrency = $currencyService->getSelectedCurrency();
    $currencyUniqueId = 'currencyDropdown' . uniqid();
@endphp
<div class="currency-switcher" data-dropdown-id="{{ $currencyUniqueId }}">
    <div class="dropdown js-dropdown js-currency-active">
        <div class="dropdown__button d-flex items-center text-13 rounded-8 px-12 py-8 lh-12"
             onclick="toggleCurrencyDropdown(event, this)"
             style="cursor: pointer;">
            <span class="js-currency-code">{{ $selectedCurrency->code }}</span>
            <i class="icon text-8 ml-8 icon-chevron-down"></i>
        </div>

        <div class="toggle-element -dropdown js-currency-toggle" id="{{ $currencyUniqueId }}">
            <!-- Search Input -->
            <div class="currency-search-wrapper">
                <input type="text"
                       class="currency-search-input"
                       placeholder="{{ __('messages.search_currency') }}"
                       onkeyup="filterCurrencies(this, '{{ $currencyUniqueId }}')"
                       onclick="event.stopPropagation();">
                <i class="currency-search-icon">🔍</i>
            </div>

            <!-- Currency List -->
            <div class="currency-list" id="currencyList-{{ $currencyUniqueId }}">
                @foreach($currencies as $currency)
                    <div class="currency-item"
                         data-code="{{ $currency->code }}"
                         data-name="{{ strtolower($currency->name) }}">
                        <a href="javascript:void(0)"
                           class="currency-link {{ $selectedCurrency->code === $currency->code ? 'active' : '' }}"
                           onclick="selectCurrency('{{ $currency->code }}')">
                            <span class="currency-code-display">{{ $currency->code }}</span>
                            <span class="currency-name-display">{{ $currency->name }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* Currency Switcher Styles */
.currency-switcher {
    position: relative;
    display: inline-block;
    margin-right: 10px;
}

.js-currency-active .dropdown__button {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.3s ease;
    cursor: pointer;
    min-width: 60px;
    font-weight: 600;
}

.js-currency-active .dropdown__button:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

/* White header styles */
.header-white .js-currency-active .dropdown__button {
    background: #f8f9fa;
    border-color: #e5e5e5;
    color: #1f2937;
}

.header-white .js-currency-active .dropdown__button:hover {
    background: #e9ecef;
}

.js-currency-toggle {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    min-width: 280px;
    max-height: 400px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    margin-top: 5px;
    overflow: hidden;
}

.js-currency-toggle.-is-el-visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Search Input */
.currency-search-wrapper {
    position: relative;
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.currency-search-input {
    width: 100%;
    padding: 10px 15px 10px 40px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

.currency-search-input:focus {
    border-color: #093162;
}

.currency-search-input::placeholder {
    color: #999;
}

.currency-search-icon {
    position: absolute;
    left: 24px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    pointer-events: none;
}

/* Currency List */
.currency-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 8px;
}

.currency-item {
    margin-bottom: 2px;
}

.currency-item.hidden {
    display: none;
}

.currency-link {
    display: flex !important;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
}

.currency-link:hover {
    background: #f5f5f5;
}

.currency-link.active {
    background: linear-gradient(135deg, #e8f4fc, #d6eaf8);
    color: #093162;
}

.currency-code-display {
    font-weight: 700;
    font-size: 14px;
    min-width: 45px;
    color: #093162;
}

.currency-link.active .currency-code-display {
    color: #005a8c;
}

.currency-name-display {
    flex: 1;
    font-size: 13px;
    color: #555;
}

.currency-link.active .currency-name-display {
    color: #093162;
    font-weight: 500;
}

/* No results message */
.no-currency-results {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 14px;
    display: none;
}

/* Scrollbar */
.currency-list::-webkit-scrollbar {
    width: 6px;
}

.currency-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.currency-list::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.currency-list::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* RTL Support */
[dir="rtl"] .currency-switcher {
    margin-right: 0;
    margin-left: 10px;
}

[dir="rtl"] .js-currency-toggle {
    left: auto;
    right: 0;
}

[dir="rtl"] .currency-search-input {
    padding: 10px 40px 10px 15px;
}

[dir="rtl"] .currency-search-icon {
    left: auto;
    right: 24px;
}

[dir="rtl"] .icon-chevron-down {
    margin-left: 0;
    margin-right: 10px;
}

/* Mobile Styles */
@media (max-width: 1199px) {
    .mobile-currency-switcher .currency-switcher,
    .mobile-currency-switcher .js-dropdown,
    .mobile-currency-switcher .js-currency-active {
        display: block;
        width: 100%;
        margin: 0 !important;
    }

    .mobile-currency-switcher .js-currency-active .dropdown__button {
        width: 100%;
        min-width: 100%;
        justify-content: space-between;
    }

    .mobile-currency-switcher .js-currency-toggle {
        position: absolute;
        top: calc(100% + 6px);
        bottom: auto;
        left: 0;
        right: 0;
        width: 100%;
        max-height: 50vh;
        border-radius: 8px;
        margin-top: 0;
        z-index: 20;
    }

    [dir="rtl"] .mobile-currency-switcher .currency-switcher {
        margin: 0 !important;
    }
}
</style>

<script>
function toggleCurrencyDropdown(event, element) {
    event.stopPropagation();
    const dropdown = element.closest('.js-dropdown');
    const toggle = dropdown.querySelector('.js-currency-toggle');

    // Close all other dropdowns
    document.querySelectorAll('.js-currency-toggle.-is-el-visible').forEach(function(el) {
        if (el !== toggle) {
            el.classList.remove('-is-el-visible');
        }
    });

    toggle.classList.toggle('-is-el-visible');

    // Focus search input when opening
    if (toggle.classList.contains('-is-el-visible')) {
        const searchInput = toggle.querySelector('.currency-search-input');
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 100);
        }
    }
}

function filterCurrencies(input, dropdownId) {
    const filter = input.value.toLowerCase().trim();
    const listId = 'currencyList-' + dropdownId;
    const list = document.getElementById(listId);
    const items = list.querySelectorAll('.currency-item');
    let visibleCount = 0;

    items.forEach(item => {
        const code = item.getAttribute('data-code').toLowerCase();
        const name = item.getAttribute('data-name');

        if (code.includes(filter) || name.includes(filter)) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    // Show/hide no results message
    let noResults = list.querySelector('.no-currency-results');
    if (!noResults) {
        noResults = document.createElement('div');
        noResults.className = 'no-currency-results';
        noResults.textContent = '{{ __("messages.no_currency_found") }}';
        list.appendChild(noResults);
    }
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}

function selectCurrency(code) {
    // Show loading state
    const buttons = document.querySelectorAll('.js-currency-active .dropdown__button');
    buttons.forEach(btn => {
        btn.style.opacity = '0.5';
        btn.style.pointerEvents = 'none';
    });

    // Send request to set currency
    fetch('{{ route("currency.set") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ currency: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update all prices
            window.location.reload();
        } else {
            alert(data.message || 'Failed to change currency');
            buttons.forEach(btn => {
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            });
        }
    })
    .catch(error => {
        console.error('Currency change error:', error);
        buttons.forEach(btn => {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        });
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.currency-switcher')) {
        document.querySelectorAll('.js-currency-toggle.-is-el-visible').forEach(function(el) {
            el.classList.remove('-is-el-visible');
        });
    }
});

// Close dropdown on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.js-currency-toggle.-is-el-visible').forEach(function(el) {
            el.classList.remove('-is-el-visible');
        });
    }
});
</script>
