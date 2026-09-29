<?php

namespace App\Filament\Pages\Auth;

use App\Models\Guru;
use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        return 'Masuk ke Sistem Absensi';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Gunakan NIP atau Email dan Password terdaftar Anda.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('NIP atau Email')
            ->placeholder('Masukkan NIP atau Email Anda')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password (Standar: NIP)')
            ->placeholder('Masukkan Password Anda (Default NIP)')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $loginInput = trim($data['email'] ?? '');

        // 1. Cari user berdasarkan NIP langsung di tabel users
        $user = User::where('nip', $loginInput)->first();

        // 2. Jika belum ketemu, cari user berdasarkan email
        if (! $user) {
            $user = User::where('email', $loginInput)->first();
        }

        // 3. Jika belum ketemu, cari user berdasarkan NIP di relasi guru
        if (! $user) {
            $guru = Guru::where('nip', $loginInput)->first();
            if ($guru && $guru->user) {
                $user = $guru->user;
            }
        }

        $email = $user ? $user->email : $loginInput;

        return [
            'email' => $email,
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'NIP / Email atau Password salah. Bagi guru, password standar awal adalah NIP Anda.',
        ]);
    }
}
