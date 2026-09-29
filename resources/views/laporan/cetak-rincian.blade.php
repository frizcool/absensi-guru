<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rincian Presensi Harian - {{ $namaBulan }} {{ $tahun }} - {{ $pengaturan->nama_sekolah }}</title>
    <link rel="icon" href="{{ $pengaturan->logo_url ?: asset('icons/icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ $pengaturan->logo_url ?: asset('icons/icon.svg') }}">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        * {
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            color: #111;
        }

        body {
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .sheet {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 297mm;
            margin: 0 auto 30px auto;
            padding: 12mm 15mm;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .kop-logo {
            width: 70px;
            text-align: center;
        }

        .kop-logo img {
            max-height: 70px;
            max-width: 70px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            padding: 0 15px;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-sekolah {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 9pt;
            margin: 2px 0 0 0;
            line-height: 1.3;
        }

        .kop-divider {
            border: none;
            border-top: 2.5px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 6px 0 12px 0;
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 12px;
        }

        .judul-laporan h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .judul-laporan p {
            font-size: 10pt;
            margin: 0;
        }

        /* Info Periode & Filter */
        .meta-table {
            width: 100%;
            font-size: 9pt;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 6px;
            border: none;
        }

        .meta-table td.label {
            font-weight: bold;
            width: 130px;
        }

        /* Tabel Presensi */
        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            table-layout: auto;
        }

        .rincian-table th, .rincian-table td {
            border: 0.8px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .rincian-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            font-size: 8pt;
        }

        .rincian-table tr {
            page-break-inside: avoid;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        .status-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
        }

        .badge-hadir { background-color: #d1fae5; color: #065f46; border: 0.5px solid #a7f3d0; }
        .badge-terlambat { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
        .badge-dinas { background-color: #e0f2fe; color: #0369a1; border: 0.5px solid #bae6fd; }
        .badge-sakit { background-color: #dbeafe; color: #1e40af; border: 0.5px solid #bfdbfe; }
        .badge-izin { background-color: #fef9c3; color: #854d0e; border: 0.5px solid #fef08a; }
        .badge-cuti { background-color: #f3e8ff; color: #6b21a8; border: 0.5px solid #e9d5ff; }
        .badge-alpa { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fecaca; }

        /* Ringkasan & Tanda Tangan */
        .summary-box {
            margin-top: 14px;
            border: 1px solid #000;
            padding: 8px 12px;
            background-color: #fafafa;
            page-break-inside: avoid;
        }

        .summary-title {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 0.5px dashed #666;
            padding-bottom: 3px;
        }

        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 20px;
            font-size: 8.5pt;
        }

        .summary-item strong {
            color: #000;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .ttd-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .ttd-space {
            height: 60px;
        }

        /* Floating Toolbar */
        .no-print-toolbar {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 9999;
            background: rgba(17, 24, 39, 0.95);
            padding: 10px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-print {
            background-color: #087f70;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background-color: #06685d;
        }

        .btn-back {
            background-color: #4b5563;
            color: #fff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .btn-back:hover {
            background-color: #374151;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .sheet {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .badge-hadir, .badge-terlambat, .badge-dinas, .badge-sakit, .badge-izin, .badge-cuti, .badge-alpa {
                border-color: #333 !important;
                color: #000 !important;
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar -->
    <div class="no-print-toolbar">
        <span style="color: #f3f4f6; font-size: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Format Kedinasan A4 Landscape</span>
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Cetak / Simpan PDF
        </button>
        <a href="javascript:window.close()" class="btn-back">Tutup</a>
    </div>

    <div class="sheet">
        <!-- KOP SURAT RESMI -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if($pengaturan->logo_url)
                        <img src="{{ $pengaturan->logo_url }}" alt="Logo Sekolah">
                    @else
                        <div style="font-size: 28pt; font-weight: bold; border: 2px solid #000; border-radius: 50%; width: 55px; height: 55px; display: inline-flex; align-items: center; justify-content: center;">🏫</div>
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-instansi">PEMERINTAH KABUPATEN / KOTA</div>
                    <div class="kop-instansi">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
                    <div class="kop-sekolah">{{ $pengaturan->nama_sekolah }}</div>
                    <div class="kop-alamat">
                        {{ $pengaturan->alamat ?: 'Alamat Sekolah Belum Diatur' }}
                        @if($pengaturan->telepon) | Telp: {{ $pengaturan->telepon }} @endif
                        @if($pengaturan->email) | Email: {{ $pengaturan->email }} @endif
                        @if($pengaturan->npsn) | NPSN: {{ $pengaturan->npsn }} @endif
                    </div>
                </td>
            </tr>
        </table>
        <div class="kop-divider"></div>

        <!-- JUDUL LAPORAN -->
        <div class="judul-laporan">
            <h2>LAPORAN RINCIAN PRESENSI &amp; JAM KERJA HARIAN GURU</h2>
            <p>Periode: Bulan <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>
        </div>

        <!-- META DATA / FILTER INFORMASI -->
        <table class="meta-table">
            <tr>
                <td class="label">Satuan Pendidikan</td>
                <td>: {{ $pengaturan->nama_sekolah }}</td>
                <td class="label">Status Pegawai</td>
                <td>: {{ $statusKepegawaian === 'semua' ? 'Semua Status (PNS, PPPK, Honorer)' : strtoupper(str_replace('_', ' ', $statusKepegawaian)) }}</td>
            </tr>
            <tr>
                <td class="label">Bulan / Tahun</td>
                <td>: {{ $namaBulan }} {{ $tahun }}</td>
                <td class="label">Shift Kerja</td>
                <td>: {{ $shift ? $shift->nama : 'Semua Shift Kerja' }}</td>
            </tr>
            <tr>
                <td class="label">Total Catatan</td>
                <td>: <strong>{{ number_format($rincian->count(), 0, ',', '.') }}</strong> catatan presensi</td>
                <td class="label">Filter Pencarian</td>
                <td>: {{ $search !== '' ? $search : '-' }}</td>
            </tr>
        </table>

        <!-- TABEL RINCIAN PRESENSI -->
        <table class="rincian-table">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 105px;">Hari, Tanggal</th>
                    <th>Nama Guru / Pegawai</th>
                    <th style="width: 110px;">Shift</th>
                    <th style="width: 75px;">Status</th>
                    <th style="width: 75px;">Masuk</th>
                    <th style="width: 75px;">Pulang</th>
                    <th style="width: 70px;">Durasi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rincian as $index => $p)
                    @php
                        $badgeClass = match ($p->status_kehadiran) {
                            'hadir' => ($p->status_masuk === 'terlambat' ? 'badge-terlambat' : 'badge-hadir'),
                            'dinas_luar' => 'badge-dinas',
                            'sakit' => 'badge-sakit',
                            'izin' => 'badge-izin',
                            'cuti' => 'badge-cuti',
                            'alpa' => 'badge-alpa',
                            default => '',
                        };

                        $statusLabel = match ($p->status_kehadiran) {
                            'hadir' => ($p->status_masuk === 'terlambat' ? 'Terlambat' : 'Hadir'),
                            'dinas_luar' => 'Dinas Luar',
                            'sakit' => 'Sakit',
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'alpa' => 'Alpa',
                            default => str($p->status_kehadiran)->replace('_', ' ')->title()->toString(),
                        };

                        $durasiKerja = '-';
                        if ($p->jam_masuk && $p->jam_pulang) {
                            $menit = abs((int) $p->jam_masuk->diffInMinutes($p->jam_pulang));
                            $durasiKerja = floor($menit / 60).'j '.($menit % 60).'m';
                        }
                    @endphp
                    <tr class="detail-row">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center" style="white-space: nowrap;">
                            {{ $p->tanggal ? $p->tanggal->locale('id')->translatedFormat('D, d/m/Y') : '-' }}
                        </td>
                        <td class="text-left">
                            <strong>{{ $p->guru?->nama ?? 'Guru tidak ditemukan' }}</strong>
                            <div style="font-size: 7.5pt; color: #444;">
                                {{ $p->guru?->nip ? 'NIP. '.$p->guru->nip : ($p->guru?->nuptk ? 'NUPTK. '.$p->guru->nuptk : 'Non-NIP') }}
                                @if($p->guru?->jabatan) &bull; {{ $p->guru->jabatan }} @endif
                            </div>
                        </td>
                        <td class="text-center">{{ $p->shift?->nama ?? '-' }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-center">
                            @if($p->jam_masuk)
                                <strong>{{ $p->jam_masuk->format('H:i') }}</strong>
                                @if($p->status_masuk === 'terlambat')
                                    <div style="font-size: 7pt; color: #b45309; font-weight: bold;">Terlambat</div>
                                @elseif($p->status_masuk === 'tepat_waktu')
                                    <div style="font-size: 7pt; color: #047857;">Tepat Waktu</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->jam_pulang)
                                <strong>{{ $p->jam_pulang->format('H:i') }}</strong>
                                @if($p->status_pulang)
                                    <div style="font-size: 7pt; color: #4b5563;">{{ str($p->status_pulang)->replace('_', ' ')->title() }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center font-bold" style="white-space: nowrap;">{{ $durasiKerja }}</td>
                        <td class="text-left" style="font-size: 8pt;">{{ $p->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 25px 10px; color: #666; font-style: italic;">
                            Tidak ada catatan presensi yang sesuai dengan parameter filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- REKAPITULASI STATISTIK -->
        <div class="summary-box">
            <div class="summary-title">Rekapitulasi Kehadiran Periode {{ $namaBulan }} {{ $tahun }}</div>
            <div class="summary-grid">
                <div class="summary-item">Total Catatan: <strong>{{ $stats['total'] }}</strong></div>
                <div class="summary-item">Hadir Tepat Waktu: <strong>{{ $stats['tepat_waktu'] }}</strong></div>
                <div class="summary-item">Terlambat: <strong>{{ $stats['terlambat'] }}</strong></div>
                <div class="summary-item">Dinas Luar: <strong>{{ $stats['dinas_luar'] }}</strong></div>
                <div class="summary-item">Sakit: <strong>{{ $stats['sakit'] }}</strong></div>
                <div class="summary-item">Izin: <strong>{{ $stats['izin'] }}</strong></div>
                <div class="summary-item">Cuti: <strong>{{ $stats['cuti'] }}</strong></div>
                <div class="summary-item">Alpa: <strong>{{ $stats['alpa'] }}</strong></div>
            </div>
        </div>

        <!-- TANDA TANGAN RESMI -->
        <table class="ttd-table">
            <tr>
                <td>
                    Mengetahui / Diperiksa oleh,<br>
                    <strong>Pengelola Presensi / Kepegawaian</strong>
                    <div class="ttd-space"></div>
                    <strong><u>( ............................................................ )</u></strong><br>
                    NIP. .......................................................
                </td>
                <td>
                    {{ $pengaturan->nama_sekolah ? explode(' ', $pengaturan->nama_sekolah)[0] : 'Makassar' }}, {{ now()->translatedFormat('d F Y') }}<br>
                    Kepala {{ $pengaturan->nama_sekolah }},
                    <div class="ttd-space"></div>
                    <strong><u>{{ $pengaturan->kepala_sekolah ?: '( Nama Kepala Sekolah Belum Diatur )' }}</u></strong><br>
                    NIP. {{ $pengaturan->nip_kepala_sekolah ?: '-' }}
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
