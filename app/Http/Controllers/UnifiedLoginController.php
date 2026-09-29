<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UnifiedLoginController extends Controller
{
    public function create(): mixed
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $pengaturan = PengaturanSekolah::getSetting();

        return view('auth.login', compact('pengaturan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $throttleKey = strtolower($validated['identity']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'identity' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
            ]);
        }

        $user = User::query()
            ->where('email', $validated['identity'])
            ->orWhere('nip', $validated['identity'])
            ->first();

        if (! $user && ($guru = Guru::query()->where('nip', $validated['identity'])->first())) {
            $user = $guru->user ?? $guru->ensureUserAccountExists();
        }

        $remember = (bool) ($validated['remember'] ?? false);
        $authenticated = false;

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $validated['password']], $remember)) {
            $authenticated = true;
        } elseif ($user && ($user->hasRole('guru') || $user->guru !== null)) {
            // Kompatibilitas login Guru: menerima password default 'password' atau NIP jika belum diubah
            if ($validated['password'] === 'password' && $user->nip && Hash::check($user->nip, $user->password)) {
                Auth::login($user, $remember);
                $authenticated = true;
            } elseif ($user->nip && $validated['password'] === $user->nip && Hash::check('password', $user->password)) {
                Auth::login($user, $remember);
                $authenticated = true;
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'identity' => 'NIP/email atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect($user->hasRole('guru') || $user->guru !== null ? '/guru' : '/sekolahku/panel');
    }
}
