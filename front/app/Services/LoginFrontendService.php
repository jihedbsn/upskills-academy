<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Student;
use App\Services\ReferralDiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginFrontendService
{
    public function index()
    {
        Log::info('Login index page accessed');

        return view('login.login');
    }

    public function login(Request $request)
    {
        Log::info('=== LOGIN METHOD CALLED ===', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'remember' => $request->has('remember'),
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $student = Student::where('email', $request->email)->first();
        if ($student && $student->is_blocked) {
            Log::warning('Login attempt for blocked user', ['email' => $request->email]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.account_blocked'),
                    'errors' => [
                        'email' => __('messages.account_blocked'),
                    ],
                ], 403);
            }

            return back()->withErrors([
                'email' => __('messages.account_blocked'),
            ])->onlyInput('email');
        }

        $remember = $request->has('remember') && $request->remember == 'on';

        if (Auth::attempt($credentials, $remember)) {
            Log::info('Auth attempt successful', ['user_id' => Auth::id()]);

            $request->session()->regenerate();
            $request->session()->save();

            Log::info('Session regenerated and saved', [
                'session_id' => session()->getId(),
                'auth_check' => Auth::check(),
                'user_id' => Auth::id(),
                'session_data' => session()->all(),
            ]);

            $itemsTransferred = $this->transferSessionCartToUser(Auth::id());

            $this->flashReferralDiscountPopup(Auth::id());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.login_successful'),
                    'items_transferred' => $itemsTransferred,
                    'redirect' => $request->input('redirect', null),
                ]);
            }

            if ($itemsTransferred > 0) {
                return redirect()->intended(route('student-dashboard.index'))->with('success', __('messages.cart_transferred_successfully', ['count' => $itemsTransferred]));
            }

            Log::info('Redirecting to dashboard', ['user_id' => Auth::id()]);

            return redirect()->intended(route('student-dashboard.index'));
        }

        Log::warning('Login failed - invalid credentials', ['email' => $request->email]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_credentials'),
                'errors' => [
                    'email' => __('login.invalid_credentials'),
                ],
            ], 422);
        }

        return back()->withErrors([
            'email' => __('login.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $this->saveUserCartToSession(Auth::id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $student = $this->findOrCreateStudent($facebookUser, 'facebook');

            Auth::login($student);
            $this->transferSessionCartToUser($student->id);
            $this->flashReferralDiscountPopup($student->id);

            return redirect()->intended(route('student-dashboard.index'))
                ->with('success', __('login.login_success'));
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Facebook login failed. Please try again.');
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $student = $this->findOrCreateStudent($googleUser, 'google');

            Auth::login($student);
            $this->transferSessionCartToUser($student->id);
            $this->flashReferralDiscountPopup($student->id);

            return redirect()->intended(route('student-dashboard.index'))
                ->with('success', __('login.login_success'));
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }
    }

    private function flashReferralDiscountPopup(?int $studentId): void
    {
        if (!$studentId) {
            return;
        }

        $percent = ReferralDiscountService::percentFor($studentId);
        if ($percent <= 0) {
            return;
        }

        session()->flash('referral_discount_popup', [
            'percent' => $percent,
        ]);
    }

    private function transferSessionCartToUser($userId)
    {
        $sessionCart = session('cart', []);

        if (empty($sessionCart)) {
            return 0;
        }

        $itemsTransferred = 0;

        foreach ($sessionCart as $itemKey => $item) {
            $parts = explode('_', $itemKey);
            if (count($parts) >= 2) {
                $itemType = $parts[0];
                $itemId = $parts[1];

                $existingCartItem = Cart::where('student_id', $userId)
                    ->where(function ($query) use ($itemType, $itemId) {
                        switch ($itemType) {
                            case 'book':
                                $query->where('book_id', $itemId);
                                break;
                            case 'course':
                                $query->where('product_id', $itemId);
                                break;
                            case 'package':
                                $query->where('package_id', $itemId);
                                break;
                        }
                    })
                    ->first();

                if (!$existingCartItem) {
                    $cartData = [
                        'student_id' => $userId,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];

                    switch ($itemType) {
                        case 'book':
                            $cartData['book_id'] = $itemId;
                            break;
                        case 'course':
                            $cartData['product_id'] = $itemId;
                            break;
                        case 'package':
                            $cartData['package_id'] = $itemId;
                            break;
                    }

                    Cart::create($cartData);
                    $itemsTransferred++;
                } else {
                    $existingCartItem->update([
                        'quantity' => $existingCartItem->quantity + $item['quantity'],
                    ]);
                    $itemsTransferred++;
                }
            }
        }

        session()->forget('cart');
        session()->forget('cart_count');

        return $itemsTransferred;
    }

    private function saveUserCartToSession($userId)
    {
        $userCart = Cart::where('student_id', $userId)->get();

        if ($userCart->isEmpty()) {
            return;
        }

        $sessionCart = [];
        $totalQuantity = 0;

        foreach ($userCart as $cartItem) {
            $itemKey = '';
            $itemData = [
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ];

            if ($cartItem->book_id) {
                $itemKey = 'book_' . $cartItem->book_id;
                $itemData['book'] = $cartItem->book;
            } elseif ($cartItem->product_id) {
                $itemKey = 'course_' . $cartItem->product_id;
                $itemData['product'] = $cartItem->product;
            } elseif ($cartItem->package_id) {
                $itemKey = 'package_' . $cartItem->package_id;
                $itemData['package'] = $cartItem->package;
            }

            if ($itemKey) {
                $sessionCart[$itemKey] = $itemData;
                $totalQuantity += $cartItem->quantity;
            }
        }

        session(['cart' => $sessionCart]);
        session(['cart_count' => $totalQuantity]);
    }

    private function findOrCreateStudent($socialUser, $provider)
    {
        $providerIdField = $provider . '_id';

        $student = Student::where($providerIdField, $socialUser->getId())->first();

        if ($student) {
            return $student;
        }

        $student = Student::where('email', $socialUser->getEmail())->first();

        if ($student) {
            $student->update([
                $providerIdField => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(),
            ]);

            return $student;
        }

        return Student::create([
            'first_name' => $socialUser->getName() ? explode(' ', $socialUser->getName())[0] : 'User',
            'last_name' => $socialUser->getName() ? implode(' ', array_slice(explode(' ', $socialUser->getName()), 1)) : '',
            'email' => $socialUser->getEmail(),
            'password' => bcrypt(Str::random(16)),
            $providerIdField => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(),
            'country' => 'Unknown',
        ]);
    }
}
