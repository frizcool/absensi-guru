@php
    $pengaturan = \App\Models\PengaturanSekolah::getSetting();
    $logoUrl = $pengaturan->logo_url;
    $namaSekolah = $pengaturan->nama_sekolah ?: ($title ?? 'Absensi Guru');
    $subTitle = ($panel ?? 'admin') === 'guru' ? 'Portal Guru & Presensi' : 'Panel Manajemen Sekolah';
@endphp

@if ($logoUrl)
    <div style="display: flex; align-items: center; gap: 10px; height: 100%; max-height: 42px; overflow: hidden; text-decoration: none;" title="{{ $namaSekolah }}">
        <div style="width: 36px; height: 36px; min-width: 36px; max-width: 36px; max-height: 36px; border-radius: 8px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-sizing: border-box; padding: 2px;">
            <img src="{{ $logoUrl }}" alt="Logo {{ $namaSekolah }}" style="max-width: 32px; max-height: 32px; width: auto; height: auto; object-fit: contain; display: block; margin: auto;">
        </div>
        <div style="display: flex; flex-direction: column; justify-content: center; min-width: 0; text-align: left; overflow: hidden; line-height: 1.25;">
            <span style="font-weight: 700; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 175px; color: inherit;">
                {{ $namaSekolah }}
            </span>
            <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 175px; color: #10b981;">
                {{ $subTitle }}
            </span>
        </div>
    </div>
@else
    <div style="display: flex; align-items: center; gap: 10px; height: 100%; max-height: 42px; overflow: hidden; text-decoration: none;" title="{{ $namaSekolah }}">
        <div style="width: 36px; height: 36px; min-width: 36px; max-width: 36px; max-height: 36px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-sizing: border-box;">
            🏫
        </div>
        <div style="display: flex; flex-direction: column; justify-content: center; min-width: 0; text-align: left; overflow: hidden; line-height: 1.25;">
            <span style="font-weight: 700; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 175px; color: inherit;">
                {{ $namaSekolah }}
            </span>
            <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 175px; color: #10b981;">
                {{ $subTitle }}
            </span>
        </div>
    </div>
@endif
