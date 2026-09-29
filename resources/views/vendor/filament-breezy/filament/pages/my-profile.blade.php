<x-filament-panels::page>
    @php
        $user = auth()->user();
        $roles = $user?->roles?->pluck('name') ?? collect();
        $guru = $user?->guru;
    @endphp

    <style>
        /* Scoped CSS Design System untuk Halaman Profil */
        :root {
            --prof-bg-card: #ffffff;
            --prof-border: #e2e8f0;
            --prof-text-main: #0f172a;
            --prof-text-muted: #64748b;
            --prof-text-sub: #475569;
            --prof-badge-bg: #f1f5f9;
        }

        .dark, html.dark, .fi-theme-dark {
            --prof-bg-card: #18181b;
            --prof-border: #27272a;
            --prof-text-main: #f4f4f5;
            --prof-text-muted: #a1a1aa;
            --prof-text-sub: #cbd5e1;
            --prof-badge-bg: #27272a;
        }

        .prof-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            font-family: inherit;
        }

        /* 1. Hero Overview Profile Banner */
        .prof-hero-banner {
            position: relative;
            background: linear-gradient(135deg, #059669 0%, #0d9488 45%, #0f172a 100%);
            border-radius: 20px;
            padding: 32px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.25);
            overflow: hidden;
        }
        .prof-hero-banner::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .prof-hero-banner::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: 25%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.25) 0%, rgba(52, 211, 153, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .prof-hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }
        .prof-avatar-box {
            position: relative;
            width: 88px;
            height: 88px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .prof-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .prof-avatar-initials {
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .prof-online-dot {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 14px;
            height: 14px;
            background: #34d399;
            border: 2px solid #0f172a;
            border-radius: 50%;
        }
        .prof-user-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 260px;
        }
        .prof-name-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .prof-user-name {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
            color: #ffffff;
        }
        .prof-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: #ffffff;
        }
        .prof-user-email {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .prof-meta-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .prof-meta-chip {
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* 2. Custom Section Container */
        .prof-sections-stack {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        /* Icon Dimensions Fix */
        .prof-wrapper svg {
            max-width: 24px !important;
            max-height: 24px !important;
        }
        .prof-wrapper .fi-icon {
            max-width: 20px !important;
            max-height: 20px !important;
        }
    </style>

    <div class="prof-wrapper">
        <!-- 1. Hero Overview Profile Banner -->
        <div class="prof-hero-banner">
            <div class="prof-hero-content">
                <!-- Avatar Preview -->
                <div class="prof-avatar-box">
                    @if ($user?->avatar_url)
                        <img src="{{ Storage::url($user->avatar_url) }}" alt="{{ $user->name }}" class="prof-avatar-img" />
                    @else
                        <span class="prof-avatar-initials">{{ strtoupper(substr($user?->name ?? 'U', 0, 2)) }}</span>
                    @endif
                    <span class="prof-online-dot" title="Akun Aktif"></span>
                </div>

                <!-- Info User -->
                <div class="prof-user-info">
                    <div class="prof-name-row">
                        <h1 class="prof-user-name">{{ $user?->name }}</h1>
                        @foreach ($roles as $role)
                            @php
                                $roleLabel = match($role) {
                                    'super_admin' => '👑 Super Admin',
                                    'admin' => '⚙️ Admin Sekolah',
                                    'kepala_sekolah' => '🎓 Kepala Sekolah',
                                    'guru' => '👨‍🏫 Guru / Tenaga Pendidik',
                                    default => ucfirst(str_replace('_', ' ', $role)),
                                };
                            @endphp
                            <span class="prof-role-badge">{{ $roleLabel }}</span>
                        @endforeach
                    </div>

                    <p class="prof-user-email">
                        <span>✉️ {{ $user?->email }}</span>
                        @if ($guru?->nip)
                            <span>&bull;</span>
                            <span>🆔 NIP: {{ $guru->nip }}</span>
                        @endif
                        @if ($guru?->jabatan)
                            <span>&bull;</span>
                            <span>💼 {{ $guru->jabatan }}</span>
                        @endif
                    </p>

                    <div class="prof-meta-chips">
                        <div class="prof-meta-chip">
                            <span>📅</span>
                            <span>Terdaftar: <b>{{ $user?->created_at?->translatedFormat('d F Y') ?? '-' }}</b></span>
                        </div>
                        <div class="prof-meta-chip">
                            <span>🛡️</span>
                            <span>2FA: <b>{{ $user?->hasEnabledTwoFactor() ? 'Aktif (Aman)' : 'Belum Diaktifkan' }}</b></span>
                        </div>
                        @if ($guru?->shift?->nama)
                            <div class="prof-meta-chip">
                                <span>⏰</span>
                                <span>Shift: <b>{{ $guru->shift->nama }}</b></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Form Components Stack (Full Width, No Awkward Aside Gaps) -->
        <div class="prof-sections-stack">
            @foreach ($this->getRegisteredMyProfileComponents() as $component)
                @unless(is_null($component))
                    <div>
                        @livewire($component)
                    </div>
                @endunless
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
