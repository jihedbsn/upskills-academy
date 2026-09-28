<?php

namespace App\Services;

use Illuminate\Http\Request;

class CurrencyFrontendService
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $currencies = $this->currencyService->getActiveCurrencies();
        $selected = $this->currencyService->getSelectedCurrency();

        return response()->json([
            'currencies' => $currencies,
            'selected' => $selected,
        ]);
    }

    public function set(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);

        $code = strtoupper((string) $request->currency);
        $success = $this->currencyService->setSelectedCurrency($code);

        if ($success) {
            $currency = $this->currencyService->getSelectedCurrency();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'currency' => $currency,
                    'message' => __('messages.currency_changed'),
                ]);
            }

            return redirect()->back()->with('success', __('messages.currency_changed'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_currency'),
            ], 400);
        }

        return redirect()->back()->with('error', __('messages.invalid_currency'));
    }

    public function detect()
    {
        $detectedCode = $this->currencyService->detectCurrencyFromGeo();
        $currency = $this->currencyService->getCurrency($detectedCode);

        return response()->json([
            'detected' => $detectedCode,
            'currency' => $currency,
        ]);
    }

    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'to' => 'nullable|string|size:3',
        ]);

        $amount = (float) $request->amount;
        $toCurrency = $request->to ? strtoupper((string) $request->to) : null;

        $converted = $this->currencyService->convert($amount, $toCurrency);
        $formatted = $this->currencyService->format($amount, $toCurrency);

        return response()->json([
            'original' => $amount,
            'converted' => $converted,
            'formatted' => $formatted,
        ]);
    }

    public function location()
    {
        $location = $this->currencyService->getDetectedLocation();
        $selected = $this->currencyService->getSelectedCurrency();

        return response()->json([
            'location' => $location,
            'selected_currency' => $selected,
            'detected_currency' => $location['currency'] ?? 'USD',
        ]);
    }

    public function redetect()
    {
        $detectedCode = $this->currencyService->forceDetectCurrency();
        $currency = $this->currencyService->getCurrency($detectedCode);
        $location = $this->currencyService->getDetectedLocation();

        return response()->json([
            'success' => true,
            'detected' => $detectedCode,
            'currency' => $currency,
            'location' => $location,
            'message' => __('messages.currency_changed'),
        ]);
    }
}
