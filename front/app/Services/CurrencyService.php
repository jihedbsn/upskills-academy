<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    /**
     * Cache keys
     */
    const RATES_CACHE_KEY = 'exchange_rates_realtime';
    const RATES_CACHE_TTL = 1800; // 30 minutes cache for real-time rates

    /**
     * Session key for selected currency
     */
    const SESSION_KEY = 'selected_currency';

    /**
     * Default currency code
     */
    const DEFAULT_CURRENCY = 'USD';

    /**
     * Free Open-Source Exchange Rate APIs (no API key required)
     * Order matters - first successful one is used
     */
    protected array $exchangeRateApis = [
        // ExchangeRate-API free tier - has all currencies including Gulf currencies (QAR, SAR, AED, etc.)
        'exchangerate-api' => 'https://open.er-api.com/v6/latest/USD',
        // Frankfurter - Open-source, uses European Central Bank data (limited currencies)
        'frankfurter' => 'https://api.frankfurter.app/latest?from=USD',
        // Currency API (free, hosted on jsDelivr)
        'currency-api' => 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json',
    ];

    /**
     * Supported currencies with their display info - ALL WORLD CURRENCIES
     */
    protected array $supportedCurrencies = [
        // Major Currencies
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'decimal_places' => 2],
        'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'decimal_places' => 0],
        'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF', 'decimal_places' => 2],
        'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'decimal_places' => 2],
        'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'decimal_places' => 2],
        'NZD' => ['name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'decimal_places' => 2],

        // Middle East & Gulf
        'SAR' => ['name' => 'Saudi Riyal', 'symbol' => 'SAR', 'decimal_places' => 2],
        'AED' => ['name' => 'UAE Dirham', 'symbol' => 'AED', 'decimal_places' => 2],
        'QAR' => ['name' => 'Qatari Riyal', 'symbol' => 'QAR', 'decimal_places' => 2],
        'KWD' => ['name' => 'Kuwaiti Dinar', 'symbol' => 'KWD', 'decimal_places' => 3],
        'BHD' => ['name' => 'Bahraini Dinar', 'symbol' => 'BHD', 'decimal_places' => 3],
        'OMR' => ['name' => 'Omani Rial', 'symbol' => 'OMR', 'decimal_places' => 3],
        'JOD' => ['name' => 'Jordanian Dinar', 'symbol' => 'JOD', 'decimal_places' => 3],
        'LBP' => ['name' => 'Lebanese Pound', 'symbol' => 'LBP', 'decimal_places' => 2],
        'ILS' => ['name' => 'Israeli Shekel', 'symbol' => '₪', 'decimal_places' => 2],
        'IQD' => ['name' => 'Iraqi Dinar', 'symbol' => 'IQD', 'decimal_places' => 3],
        'IRR' => ['name' => 'Iranian Rial', 'symbol' => 'IRR', 'decimal_places' => 2],
        'SYP' => ['name' => 'Syrian Pound', 'symbol' => 'SYP', 'decimal_places' => 2],
        'YER' => ['name' => 'Yemeni Rial', 'symbol' => 'YER', 'decimal_places' => 2],

        // Africa
        'EGP' => ['name' => 'Egyptian Pound', 'symbol' => 'EGP', 'decimal_places' => 2],
        'MAD' => ['name' => 'Moroccan Dirham', 'symbol' => 'MAD', 'decimal_places' => 2],
        'TND' => ['name' => 'Tunisian Dinar', 'symbol' => 'TND', 'decimal_places' => 3],
        'DZD' => ['name' => 'Algerian Dinar', 'symbol' => 'DZD', 'decimal_places' => 2],
        'LYD' => ['name' => 'Libyan Dinar', 'symbol' => 'LYD', 'decimal_places' => 3],
        'SDG' => ['name' => 'Sudanese Pound', 'symbol' => 'SDG', 'decimal_places' => 2],
        'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'decimal_places' => 2],
        'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2],
        'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KES', 'decimal_places' => 2],
        'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => 'GHS', 'decimal_places' => 2],
        'ETB' => ['name' => 'Ethiopian Birr', 'symbol' => 'ETB', 'decimal_places' => 2],
        'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TZS', 'decimal_places' => 2],
        'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'UGX', 'decimal_places' => 0],
        'XOF' => ['name' => 'West African CFA', 'symbol' => 'XOF', 'decimal_places' => 0],
        'XAF' => ['name' => 'Central African CFA', 'symbol' => 'XAF', 'decimal_places' => 0],

        // Europe
        'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr', 'decimal_places' => 2],
        'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'kr', 'decimal_places' => 2],
        'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr', 'decimal_places' => 2],
        'PLN' => ['name' => 'Polish Zloty', 'symbol' => 'zł', 'decimal_places' => 2],
        'CZK' => ['name' => 'Czech Koruna', 'symbol' => 'Kč', 'decimal_places' => 2],
        'HUF' => ['name' => 'Hungarian Forint', 'symbol' => 'Ft', 'decimal_places' => 2],
        'RON' => ['name' => 'Romanian Leu', 'symbol' => 'lei', 'decimal_places' => 2],
        'BGN' => ['name' => 'Bulgarian Lev', 'symbol' => 'лв', 'decimal_places' => 2],
        'HRK' => ['name' => 'Croatian Kuna', 'symbol' => 'kn', 'decimal_places' => 2],
        'RSD' => ['name' => 'Serbian Dinar', 'symbol' => 'RSD', 'decimal_places' => 2],
        'UAH' => ['name' => 'Ukrainian Hryvnia', 'symbol' => '₴', 'decimal_places' => 2],
        'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽', 'decimal_places' => 2],
        'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'decimal_places' => 2],
        'ISK' => ['name' => 'Icelandic Krona', 'symbol' => 'kr', 'decimal_places' => 0],

        // Asia
        'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'decimal_places' => 2],
        'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'decimal_places' => 2],
        'TWD' => ['name' => 'Taiwan Dollar', 'symbol' => 'NT$', 'decimal_places' => 2],
        'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'decimal_places' => 2],
        'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'decimal_places' => 2],
        'THB' => ['name' => 'Thai Baht', 'symbol' => '฿', 'decimal_places' => 2],
        'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'decimal_places' => 0],
        'PHP' => ['name' => 'Philippine Peso', 'symbol' => '₱', 'decimal_places' => 2],
        'VND' => ['name' => 'Vietnamese Dong', 'symbol' => '₫', 'decimal_places' => 0],
        'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩', 'decimal_places' => 0],
        'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'decimal_places' => 2],
        'PKR' => ['name' => 'Pakistani Rupee', 'symbol' => 'Rs', 'decimal_places' => 2],
        'BDT' => ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'decimal_places' => 2],
        'LKR' => ['name' => 'Sri Lankan Rupee', 'symbol' => 'Rs', 'decimal_places' => 2],
        'NPR' => ['name' => 'Nepalese Rupee', 'symbol' => 'Rs', 'decimal_places' => 2],
        'MMK' => ['name' => 'Myanmar Kyat', 'symbol' => 'K', 'decimal_places' => 2],
        'KHR' => ['name' => 'Cambodian Riel', 'symbol' => '៛', 'decimal_places' => 2],
        'LAK' => ['name' => 'Lao Kip', 'symbol' => '₭', 'decimal_places' => 2],
        'MNT' => ['name' => 'Mongolian Tugrik', 'symbol' => '₮', 'decimal_places' => 2],
        'KZT' => ['name' => 'Kazakhstani Tenge', 'symbol' => '₸', 'decimal_places' => 2],
        'UZS' => ['name' => 'Uzbekistani Som', 'symbol' => 'UZS', 'decimal_places' => 2],
        'AZN' => ['name' => 'Azerbaijani Manat', 'symbol' => '₼', 'decimal_places' => 2],
        'GEL' => ['name' => 'Georgian Lari', 'symbol' => '₾', 'decimal_places' => 2],
        'AMD' => ['name' => 'Armenian Dram', 'symbol' => '֏', 'decimal_places' => 2],

        // Americas
        'MXN' => ['name' => 'Mexican Peso', 'symbol' => 'MX$', 'decimal_places' => 2],
        'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$', 'decimal_places' => 2],
        'ARS' => ['name' => 'Argentine Peso', 'symbol' => 'AR$', 'decimal_places' => 2],
        'CLP' => ['name' => 'Chilean Peso', 'symbol' => 'CL$', 'decimal_places' => 0],
        'COP' => ['name' => 'Colombian Peso', 'symbol' => 'CO$', 'decimal_places' => 2],
        'PEN' => ['name' => 'Peruvian Sol', 'symbol' => 'S/', 'decimal_places' => 2],
        'UYU' => ['name' => 'Uruguayan Peso', 'symbol' => '$U', 'decimal_places' => 2],
        'VES' => ['name' => 'Venezuelan Bolivar', 'symbol' => 'Bs', 'decimal_places' => 2],
        'BOB' => ['name' => 'Bolivian Boliviano', 'symbol' => 'Bs', 'decimal_places' => 2],
        'PYG' => ['name' => 'Paraguayan Guarani', 'symbol' => '₲', 'decimal_places' => 0],
        'DOP' => ['name' => 'Dominican Peso', 'symbol' => 'RD$', 'decimal_places' => 2],
        'CRC' => ['name' => 'Costa Rican Colon', 'symbol' => '₡', 'decimal_places' => 2],
        'GTQ' => ['name' => 'Guatemalan Quetzal', 'symbol' => 'Q', 'decimal_places' => 2],
        'HNL' => ['name' => 'Honduran Lempira', 'symbol' => 'L', 'decimal_places' => 2],
        'NIO' => ['name' => 'Nicaraguan Cordoba', 'symbol' => 'C$', 'decimal_places' => 2],
        'PAB' => ['name' => 'Panamanian Balboa', 'symbol' => 'B/', 'decimal_places' => 2],
        'JMD' => ['name' => 'Jamaican Dollar', 'symbol' => 'J$', 'decimal_places' => 2],
        'TTD' => ['name' => 'Trinidad Dollar', 'symbol' => 'TT$', 'decimal_places' => 2],
        'BBD' => ['name' => 'Barbadian Dollar', 'symbol' => 'Bds$', 'decimal_places' => 2],
        'BSD' => ['name' => 'Bahamian Dollar', 'symbol' => 'B$', 'decimal_places' => 2],
        'HTG' => ['name' => 'Haitian Gourde', 'symbol' => 'G', 'decimal_places' => 2],
        'CUP' => ['name' => 'Cuban Peso', 'symbol' => '₱', 'decimal_places' => 2],

        // Oceania
        'FJD' => ['name' => 'Fijian Dollar', 'symbol' => 'FJ$', 'decimal_places' => 2],
        'PGK' => ['name' => 'Papua New Guinean Kina', 'symbol' => 'K', 'decimal_places' => 2],
        'WST' => ['name' => 'Samoan Tala', 'symbol' => 'WS$', 'decimal_places' => 2],
        'TOP' => ['name' => 'Tongan Paanga', 'symbol' => 'T$', 'decimal_places' => 2],
        'VUV' => ['name' => 'Vanuatu Vatu', 'symbol' => 'VT', 'decimal_places' => 0],

        // Other
        'XAU' => ['name' => 'Gold (oz)', 'symbol' => 'XAU', 'decimal_places' => 2],
        'XAG' => ['name' => 'Silver (oz)', 'symbol' => 'XAG', 'decimal_places' => 2],
        'BTC' => ['name' => 'Bitcoin', 'symbol' => '₿', 'decimal_places' => 8],
    ];

    /**
     * Country to currency mapping for geo-detection (ISO 3166-1 alpha-2 to ISO 4217)
     */
    protected array $countryToCurrency = [
        // North America
        'US' => 'USD', 'CA' => 'CAD', 'MX' => 'MXN',

        // Europe - Eurozone
        'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR',
        'BE' => 'EUR', 'AT' => 'EUR', 'PT' => 'EUR', 'IE' => 'EUR', 'FI' => 'EUR',
        'GR' => 'EUR', 'SK' => 'EUR', 'SI' => 'EUR', 'LT' => 'EUR', 'LV' => 'EUR',
        'EE' => 'EUR', 'CY' => 'EUR', 'MT' => 'EUR', 'LU' => 'EUR', 'MC' => 'EUR',
        'SM' => 'EUR', 'VA' => 'EUR', 'AD' => 'EUR', 'ME' => 'EUR', 'XK' => 'EUR',

        // Europe - Non-Eurozone
        'GB' => 'GBP', 'CH' => 'CHF', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK',
        'PL' => 'PLN', 'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON', 'BG' => 'BGN',
        'HR' => 'EUR', 'RS' => 'RSD', 'UA' => 'UAH', 'RU' => 'RUB', 'BY' => 'BYN',
        'MD' => 'MDL', 'AL' => 'ALL', 'MK' => 'MKD', 'BA' => 'BAM', 'IS' => 'ISK',

        // Middle East & Gulf
        'SA' => 'SAR', 'AE' => 'AED', 'QA' => 'QAR', 'KW' => 'KWD', 'BH' => 'BHD',
        'OM' => 'OMR', 'JO' => 'JOD', 'LB' => 'LBP', 'IL' => 'ILS', 'IQ' => 'IQD',
        'IR' => 'IRR', 'SY' => 'SYP', 'YE' => 'YER', 'PS' => 'ILS',

        // Africa - North
        'EG' => 'EGP', 'MA' => 'MAD', 'TN' => 'TND', 'DZ' => 'DZD', 'LY' => 'LYD', 'SD' => 'SDG',

        // Africa - Sub-Saharan
        'ZA' => 'ZAR', 'NG' => 'NGN', 'KE' => 'KES', 'GH' => 'GHS', 'ET' => 'ETB',
        'TZ' => 'TZS', 'UG' => 'UGX', 'RW' => 'RWF', 'AO' => 'AOA', 'MZ' => 'MZN',
        'ZM' => 'ZMW', 'ZW' => 'ZWL', 'BW' => 'BWP', 'NA' => 'NAD', 'MU' => 'MUR',
        'SN' => 'XOF', 'CI' => 'XOF', 'ML' => 'XOF', 'BF' => 'XOF', 'NE' => 'XOF',
        'TG' => 'XOF', 'BJ' => 'XOF', 'GW' => 'XOF', 'CM' => 'XAF', 'GA' => 'XAF',
        'CG' => 'XAF', 'CD' => 'CDF', 'CF' => 'XAF', 'TD' => 'XAF', 'GQ' => 'XAF',

        // Asia - East
        'JP' => 'JPY', 'CN' => 'CNY', 'KR' => 'KRW', 'HK' => 'HKD', 'TW' => 'TWD', 'MN' => 'MNT',

        // Asia - Southeast
        'SG' => 'SGD', 'MY' => 'MYR', 'ID' => 'IDR', 'TH' => 'THB', 'VN' => 'VND',
        'PH' => 'PHP', 'MM' => 'MMK', 'KH' => 'KHR', 'LA' => 'LAK', 'BN' => 'BND',

        // Asia - South
        'IN' => 'INR', 'PK' => 'PKR', 'BD' => 'BDT', 'LK' => 'LKR', 'NP' => 'NPR',
        'BT' => 'BTN', 'MV' => 'MVR', 'AF' => 'AFN',

        // Asia - Central
        'KZ' => 'KZT', 'UZ' => 'UZS', 'TM' => 'TMT', 'KG' => 'KGS', 'TJ' => 'TJS',
        'AZ' => 'AZN', 'GE' => 'GEL', 'AM' => 'AMD',

        // Oceania
        'AU' => 'AUD', 'NZ' => 'NZD', 'FJ' => 'FJD', 'PG' => 'PGK', 'WS' => 'WST',
        'TO' => 'TOP', 'VU' => 'VUV', 'SB' => 'SBD',

        // South America
        'BR' => 'BRL', 'AR' => 'ARS', 'CL' => 'CLP', 'CO' => 'COP', 'PE' => 'PEN',
        'VE' => 'VES', 'EC' => 'USD', 'BO' => 'BOB', 'PY' => 'PYG', 'UY' => 'UYU',
        'GY' => 'GYD', 'SR' => 'SRD',

        // Central America & Caribbean
        'PA' => 'PAB', 'CR' => 'CRC', 'GT' => 'GTQ', 'HN' => 'HNL', 'NI' => 'NIO',
        'SV' => 'USD', 'BZ' => 'BZD', 'JM' => 'JMD', 'TT' => 'TTD', 'BB' => 'BBD',
        'BS' => 'BSD', 'HT' => 'HTG', 'DO' => 'DOP', 'CU' => 'CUP', 'PR' => 'USD',

        // Turkey
        'TR' => 'TRY',
    ];

    /**
     * Fetch real-time exchange rates from API (cached for 30 min)
     */
    public function getRealTimeRates(): array
    {
        return Cache::remember(self::RATES_CACHE_KEY, self::RATES_CACHE_TTL, function () {
            $rates = $this->fetchRatesFromApi();
            return !empty($rates) ? $rates : $this->getFallbackRates();
        });
    }

    /**
     * Force refresh rates from API
     */
    public function refreshRates(): array
    {
        Cache::forget(self::RATES_CACHE_KEY);
        return $this->getRealTimeRates();
    }

    /**
     * Fetch rates from external APIs
     */
    protected function fetchRatesFromApi(): array
    {
        foreach ($this->exchangeRateApis as $name => $url) {
            try {
                $response = Http::timeout(5)->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $rates = [];

                    if ($name === 'frankfurter' || $name === 'exchangerate-api') {
                        $rates = $data['rates'] ?? [];
                    } elseif ($name === 'currency-api') {
                        $rawRates = $data['usd'] ?? [];
                        foreach ($rawRates as $code => $rate) {
                            $rates[strtoupper($code)] = $rate;
                        }
                    }

                    if (!empty($rates)) {
                        $rates['USD'] = 1.0;
                        Log::info("Exchange rates fetched from {$name}");
                        return $rates;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Exchange rate API {$name} failed: " . $e->getMessage());
            }
        }

        return [];
    }

    /**
     * Fallback rates if all APIs fail
     */
    protected function getFallbackRates(): array
    {
        return [
            'USD' => 1.0, 'EUR' => 0.92, 'GBP' => 0.79, 'SAR' => 3.75,
            'AED' => 3.67, 'EGP' => 30.90, 'KWD' => 0.31, 'QAR' => 3.64,
            'SEK' => 10.50, 'CAD' => 1.35, 'AUD' => 1.53, 'CHF' => 0.88,
            'JPY' => 149.50, 'INR' => 83.10, 'TRY' => 30.20,
        ];
    }

    /**
     * Get exchange rate for a specific currency
     */
    public function getExchangeRate(string $currencyCode): float
    {
        $rates = $this->getRealTimeRates();
        return $rates[strtoupper($currencyCode)] ?? 1.0;
    }

    /**
     * Get all active currencies with real-time rates
     */
    public function getActiveCurrencies(): array
    {
        $rates = $this->getRealTimeRates();
        $currencies = [];

        foreach ($this->supportedCurrencies as $code => $info) {
            $currencies[] = (object)[
                'code' => $code,
                'name' => $info['name'],
                'symbol' => $info['symbol'],
                'exchange_rate' => $rates[$code] ?? 1.0,
                'decimal_places' => $info['decimal_places'],
            ];
        }

        return $currencies;
    }

    /**
     * Get currency by code
     */
    public function getCurrency(string $code): ?object
    {
        $code = strtoupper($code);

        if (!isset($this->supportedCurrencies[$code])) {
            return null;
        }

        $info = $this->supportedCurrencies[$code];
        $rates = $this->getRealTimeRates();

        return (object)[
            'code' => $code,
            'name' => $info['name'],
            'symbol' => $info['symbol'],
            'exchange_rate' => $rates[$code] ?? 1.0,
            'decimal_places' => $info['decimal_places'],
        ];
    }

    /**
     * Get the currently selected currency
     */
    public function getSelectedCurrency(): object
    {
        $currencyCode = Session::get(self::SESSION_KEY, self::DEFAULT_CURRENCY);
        $currency = $this->getCurrency($currencyCode);

        return $currency ?? $this->getCurrency(self::DEFAULT_CURRENCY);
    }

    /**
     * Set the selected currency
     */
    public function setSelectedCurrency(string $code): bool
    {
        $code = strtoupper($code);

        if (isset($this->supportedCurrencies[$code])) {
            Session::put(self::SESSION_KEY, $code);
            return true;
        }

        return false;
    }

    /**
     * Convert amount from USD to selected/specified currency
     */
    public function convert(float $amountUsd, ?string $toCurrency = null): float
    {
        $currency = $toCurrency
            ? $this->getCurrency($toCurrency)
            : $this->getSelectedCurrency();

        if (!$currency || $currency->code === 'USD') {
            return $amountUsd;
        }

        return $amountUsd * $currency->exchange_rate;
    }

    /**
     * Format price in selected/specified currency
     * Always uses currency code (e.g., "142.00 QAR") for consistency
     */
    public function format(?float $amountUsd, ?string $toCurrency = null, bool $showCode = true): string
    {
        $amountUsd = $amountUsd ?? 0.0;

        $currency = $toCurrency
            ? $this->getCurrency($toCurrency)
            : $this->getSelectedCurrency();

        if (!$currency) {
            return '$' . number_format($amountUsd, 2);
        }

        $convertedAmount = $this->convert($amountUsd, $currency->code);
        $formatted = number_format($convertedAmount, $currency->decimal_places);

        // Always use currency code for clarity (e.g., "142.00 QAR", "$39.00 USD")
        // For USD, use $ symbol; for others, use code
        if ($currency->code === 'USD') {
            return '$' . $formatted;
        }

        // Format: "142.00 QAR" - amount followed by currency code
        return $formatted . ' ' . $currency->code;
    }

    /**
     * Detect currency based on visitor's IP/geo-location
     */
    public function detectCurrencyFromGeo(): string
    {
        try {
            $ip = $this->getClientIp();

            // Log all IP detection for debugging
            Log::info("Currency detection - Detected IP: {$ip}, Headers: " . json_encode([
                'CF-Connecting-IP' => request()->header('CF-Connecting-IP'),
                'X-Forwarded-For' => request()->header('X-Forwarded-For'),
                'X-Real-IP' => request()->header('X-Real-IP'),
                'REMOTE_ADDR' => request()->ip(),
            ]));

            // Skip for localhost - return default
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                Log::debug("Currency detection: localhost detected, using default USD");
                return self::DEFAULT_CURRENCY;
            }

            // Try ip-api.com (free, no API key needed, 45 requests/minute limit)
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode,country");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    $countryCode = $data['countryCode'] ?? null;
                    $countryName = $data['country'] ?? 'Unknown';

                    if ($countryCode && isset($this->countryToCurrency[$countryCode])) {
                        $suggestedCurrency = $this->countryToCurrency[$countryCode];

                        // Check if this currency is in our supported list
                        if (isset($this->supportedCurrencies[$suggestedCurrency])) {
                            Log::info("Currency auto-detected: {$suggestedCurrency} for {$countryName} ({$countryCode}) - IP: {$ip}");
                            return $suggestedCurrency;
                        }
                    }

                    Log::debug("Currency detection: No mapping for country {$countryCode}, using USD");
                }
            } else {
                Log::warning("Currency detection: API call failed for IP {$ip}");
            }
        } catch (\Exception $e) {
            Log::warning('Geo currency detection failed: ' . $e->getMessage());
        }

        return self::DEFAULT_CURRENCY;
    }

    /**
     * Auto-detect and set currency if not already set
     */
    public function autoDetectCurrency(): void
    {
        if (!Session::has(self::SESSION_KEY)) {
            $detectedCurrency = $this->detectCurrencyFromGeo();
            $this->setSelectedCurrency($detectedCurrency);
            Log::debug("Currency auto-set to: {$detectedCurrency}");
        }
    }

    /**
     * Force re-detect currency from geo-location (ignores existing session)
     */
    public function forceDetectCurrency(): string
    {
        $detectedCurrency = $this->detectCurrencyFromGeo();
        $this->setSelectedCurrency($detectedCurrency);
        return $detectedCurrency;
    }

    /**
     * Get detected country info (for debugging/display)
     */
    public function getDetectedLocation(): ?array
    {
        try {
            $ip = $this->getClientIp();

            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                return ['ip' => $ip, 'country' => 'Localhost', 'countryCode' => 'XX', 'currency' => 'USD'];
            }

            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode,country,city,region");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    $countryCode = $data['countryCode'] ?? null;
                    $currency = $this->countryToCurrency[$countryCode] ?? 'USD';

                    return [
                        'ip' => $ip,
                        'country' => $data['country'] ?? 'Unknown',
                        'countryCode' => $countryCode,
                        'city' => $data['city'] ?? null,
                        'region' => $data['region'] ?? null,
                        'currency' => $currency,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::debug('Location detection failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get cache info for debugging
     */
    public function getCacheInfo(): array
    {
        $isCached = Cache::has(self::RATES_CACHE_KEY);

        return [
            'is_cached' => $isCached,
            'cache_ttl_minutes' => self::RATES_CACHE_TTL / 60,
            'rates_source' => 'Real-time API (Frankfurter/ExchangeRate-API)',
        ];
    }

    /**
     * Get the real client IP address, even behind proxies/Cloudflare
     */
    protected function getClientIp(): string
    {
        $request = request();

        // Check Cloudflare header first (most reliable when using Cloudflare)
        if ($request->hasHeader('CF-Connecting-IP')) {
            return $request->header('CF-Connecting-IP');
        }

        // Check X-Forwarded-For (common proxy header)
        if ($request->hasHeader('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            $ip = trim($ips[0]); // First IP is the original client
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        // Check X-Real-IP (nginx proxy)
        if ($request->hasHeader('X-Real-IP')) {
            $ip = $request->header('X-Real-IP');
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        // Check True-Client-IP (some CDNs)
        if ($request->hasHeader('True-Client-IP')) {
            $ip = $request->header('True-Client-IP');
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        // Fallback to standard request IP
        return $request->ip() ?? '127.0.0.1';
    }
}
