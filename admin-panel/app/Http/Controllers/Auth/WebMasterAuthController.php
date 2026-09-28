<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\WebMaster;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WebMasterAuthController extends Controller
{
    public function __construct()
    {
        // Pas de middleware guest dans cette version de Laravel
    }

    public function showLoginForm()
    {
        // Si déjà connecté en tant que webmaster, rediriger vers le dashboard
        if (auth('webmaster')->check()) {
            return redirect()->route('webmaster.dashboard');
        }
        
        return view('auth.webmaster.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Vérifier si le webmaster existe et est actif
        $webmaster = WebMaster::where('email', $credentials['email'])->first();

        if (!$webmaster || !$webmaster->is_active) {
            return redirect()->back()
                ->withErrors(['email' => 'Compte inactif ou inexistant.'])
                ->withInput($request->only('email'));
        }

        // Tentative de connexion
        if (Auth::guard('webmaster')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Mettre à jour les informations de connexion
            $webmaster->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Logger l'activité
            UserActivity::logLogin($webmaster);

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->back()
            ->withErrors(['email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $webmaster = Auth::guard('webmaster')->user();
        
        if ($webmaster) {
            // Logger l'activité
            UserActivity::logLogout($webmaster);
        }

        Auth::guard('webmaster')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('webmaster.login');
    }


    public function showProfile()
    {
        $webmaster = Auth::guard('webmaster')->user();
        return view('webmaster.profile', compact('webmaster'));
    }

    public function updateProfile(Request $request)
    {
        $webmaster = Auth::guard('webmaster')->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:web_masters,email,' . $webmaster->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:password|current_password:webmaster',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldValues = $webmaster->toArray();
        
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $webmaster->update($updateData);

        // Logger l'activité
        $webmaster->logActivity('update', $webmaster, 'Mise à jour du profil', $oldValues, $webmaster->fresh()->toArray());

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }
}
