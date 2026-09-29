<x-filament-panels::page>
    <style>
        .pi-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
        }

        .pi-table-wrapper {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }

        .dark .pi-table-wrapper {
            border-color: #1f2937;
        }

        .pi-table {
            width: 100%;
            font-size: 0.75rem;
            text-align: left;
            border-collapse: collapse;
        }

        .pi-thead {
            background: #f8fafc;
            color: #475569;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.6875rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .dark .pi-thead {
            background: #0f172a;
            color: #94a3b8;
            border-color: #1f2937;
        }

        .pi-th {
            padding: 0.85rem 1rem;
        }

        .pi-tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .dark .pi-tr {
            border-color: #1e293b;
        }

        .pi-tr:hover {
            background: #f8fafc;
        }

        .dark .pi-tr:hover {
            background: #1e293b;
        }

        .pi-td {
            padding: 0.85rem 1rem;
        }

        .pi-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .pi-badge-sakit {
            background: #dbeafe;
            color: #1e40af;
        }
        .dark .pi-badge-sakit {
            background: rgba(30, 64, 175, 0.4);
            color: #93c5fd;
        }

        .pi-badge-izin {
            background: #fef3c7;
            color: #92400e;
        }
        .dark .pi-badge-izin {
            background: rgba(146, 64, 14, 0.4);
            color: #fde68a;
        }

        .pi-badge-cuti {
            background: #f3e8ff;
            color: #6b21a8;
        }
        .dark .pi-badge-cuti {
            background: rgba(107, 33, 168, 0.4);
            color: #d8b4fe;
        }

        .pi-badge-dinas {
            background: #ccfbf1;
            color: #0f766e;
        }
        .dark .pi-badge-dinas {
            background: rgba(15, 118, 110, 0.4);
            color: #5eead4;
        }

        .pi-badge-approved {
            background: #d1fae5;
            color: #065f46;
        }
        .dark .pi-badge-approved {
            background: rgba(6, 95, 70, 0.4);
            color: #6ee7b7;
        }

        .pi-badge-rejected {
            background: #ffe4e6;
            color: #9f1239;
        }
        .dark .pi-badge-rejected {
            background: rgba(159, 18, 57, 0.4);
            color: #fda4af;
        }

        .pi-badge-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }
        .dark .pi-badge-pending {
            background: rgba(194, 65, 12, 0.3);
            color: #fdba74;
            border-color: rgba(253, 186, 116, 0.2);
        }
    </style>

    <div class="pi-container">
        <!-- Form Pengajuan Izin -->
        <form wire:submit="ajukan" style="display: flex; flex-direction: column; gap: 1.25rem;">
            {{ $this->form }}

            <div style="display: flex; justify-content: flex-end;">
                <x-filament::button type="submit" size="lg" icon="heroicon-o-paper-airplane" color="primary">
                    Kirim Pengajuan Izin / Cuti
                </x-filament::button>
            </div>
        </form>

        <!-- Tabel Riwayat Pengajuan Izin Saya -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-clock" style="width: 1.25rem; height: 1.25rem; color: #10b981;" />
                    <span style="font-weight: 800; font-size: 0.95rem;">Riwayat Pengajuan Izin & Cuti Saya</span>
                </div>
            </x-slot>

            <div class="pi-table-wrapper">
                <table class="pi-table">
                    <thead class="pi-thead">
                        <tr>
                            <th class="pi-th">Tanggal Pengajuan</th>
                            <th class="pi-th">Jenis</th>
                            <th class="pi-th">Rentang Tanggal</th>
                            <th class="pi-th">Alasan</th>
                            <th class="pi-th">Lampiran</th>
                            <th class="pi-th">Status</th>
                            <th class="pi-th">Catatan Verifikator</th>
                            <th class="pi-th" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->riwayatPengajuan as $izin)
                            <tr class="pi-tr">
                                <td class="pi-td" style="color: #64748b;">
                                    {{ $izin->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="pi-td">
                                    <span class="pi-badge {{ $izin->jenis === 'sakit' ? 'pi-badge-sakit' : ($izin->jenis === 'cuti' ? 'pi-badge-cuti' : ($izin->jenis === 'dinas_luar' ? 'pi-badge-dinas' : 'pi-badge-izin')) }}">
                                        {{ $izin->jenis === 'dinas_luar' ? 'Dinas Luar' : ucfirst($izin->jenis) }}
                                    </span>
                                </td>
                                <td class="pi-td" style="font-weight: 800;">
                                    {{ $izin->tanggal_mulai->format('d/m/Y') }} - {{ $izin->tanggal_selesai->format('d/m/Y') }}
                                </td>
                                <td class="pi-td" style="max-width: 16rem;">
                                    {{ $izin->alasan }}
                                </td>
                                <td class="pi-td">
                                    @if ($izin->lampiran)
                                        <a href="{{ asset('storage/' . $izin->lampiran) }}" target="_blank" style="color: #0d9488; font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <x-filament::icon icon="heroicon-o-paper-clip" style="width: 0.95rem; height: 0.95rem;" />
                                            <span>Lihat Bukti</span>
                                        </a>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                                <td class="pi-td">
                                    @if ($izin->status === 'disetujui')
                                        <span class="pi-badge pi-badge-approved">
                                            ✅ Disetujui
                                        </span>
                                    @elseif ($izin->status === 'ditolak')
                                        <span class="pi-badge pi-badge-rejected">
                                            ❌ Ditolak
                                        </span>
                                    @else
                                        <span class="pi-badge pi-badge-pending">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="pi-td" style="color: #64748b; font-style: italic;">
                                    {{ $izin->catatan_approval ?: '-' }}
                                </td>
                                <td class="pi-td" style="text-align: center;">
                                    @if ($izin->status === 'menunggu')
                                        <button type="button" wire:click="batalkanPengajuan({{ $izin->id }})" wire:confirm="Apakah Anda yakin ingin membatalkan pengajuan ini?" style="color: #e11d48; font-weight: 700; background: none; border: none; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <x-filament::icon icon="heroicon-o-trash" style="width: 0.95rem; height: 0.95rem;" />
                                            <span>Batalkan</span>
                                        </button>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 2.5rem; text-align: center; color: #94a3b8;">
                                    Belum ada riwayat pengajuan izin atau sakit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
