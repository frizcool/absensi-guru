<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Kedinasan - {{ $namaBulan }} {{ $tahun }} - {{ $pengaturan->nama_sekolah }}</title>
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
            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: auto;
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
            margin-bottom: 10px;
        }

        .judul-laporan h2 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .judul-laporan p {
            font-size: 9.5pt;
            margin: 0;
        }

        /* Info Periode */
        .meta-table {
            width: 100%;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .meta-table td {
            padding: 1px 4px;
            border: none;
        }

        /* Tabel Presensi */
        .presensi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            table-layout: auto;
        }

        .presensi-table th, .presensi-table td {
            border: 0.8px solid #000;
            padding: 2.5px 1.5px;
            text-align: center;
        }

        .presensi-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .presensi-table td.text-left {
            text-align: left;
            padding-left: 4px;
        }

        .presensi-table tr.libur-cell {
            background-color: #f9fafb;
        }

        .code-H { font-weight: bold; }
        .code-T { font-weight: bold; background-color: #fef3c7; }
        .code-DL { font-weight: bold; background-color: #e0f2fe; }
        .code-S { font-weight: bold; background-color: #dbeafe; }
        .code-I { font-weight: bold; background-color: #fef9c3; }
        .code-C { font-weight: bold; background-color: #f3e8ff; }
        .code-A { font-weight: bold; background-color: #fee2e2; color: #b91c1c; }
        .code-L { background-color: #e5e7eb; color: #9ca3af; }

        /* Keterangan & TTD */
        .footer-container {
            width: 100%;
            margin-top: 10px;
            font-size: 8.5pt;
        }

        .legend-box {
            font-size: 7.5pt;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 15px;
        }

        .ttd-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .ttd-space {
            height: 55px;
        }

        /* SPTJB Sheet */
        .sptjb-container {
            font-size: 10pt;
            line-height: 1.6;
        }

        .sptjb-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Floating Toolbar */
        .no-print-toolbar {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 9999;
            background: rgba(17, 24, 39, 0.9);
            padding: 10px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-print {
            background-color: #10b981;
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
            background-color: #059669;
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
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar -->
    <div class="no-print-toolbar">
        <span style="color: #f3f4f6; font-size: 12px; font-family: sans-serif;">Format Resmi Siap Cetak (A4 Landscape)</span>
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Cetak / Simpan PDF
        </button>
        <a href="javascript:window.close()" class="btn-back">Tutup</a>
    </div>

    <!-- HALAMAN 1: MATRIKS DAFTAR HADIR BULANAN -->
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
                    <div class="kop-instansi">PEMERINTAH KOTA / KABUPATEN - DINAS PENDIDIKAN</div>
                    <div class="kop-sekolah">{{ strtoupper($pengaturan->nama_sekolah) }}</div>
                    <div class="kop-alamat">
                        NPSN: {{ $pengaturan->npsn ?: '-' }} | Alamat: {{ $pengaturan->alamat ?: 'Alamat Sekolah Belum Diatur' }}<br>
                        Telp: {{ $pengaturan->telepon ?: '-' }} | Email: {{ $pengaturan->email ?: '-' }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="kop-divider"></div>

        <!-- JUDUL LAPORAN -->
        <div class="judul-laporan">
            <h2>REKAPITULASI DAFTAR HADIR GURU DAN TENAGA KEPENDIDIKAN</h2>
            <p>Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong> (Jumlah Hari Kerja Efektif: <strong>{{ $totalHariEfektif }} Hari</strong>)</p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 15%;">Unit Kerja / Sekolah</td>
                <td style="width: 45%;">: <strong>{{ $pengaturan->nama_sekolah }}</strong></td>
                <td style="width: 15%;">Status Kepegawaian</td>
                <td style="width: 25%;">: {{ strtoupper($statusKepegawaian) }}</td>
            </tr>
        </table>

        <!-- TABEL MATRIX BULANAN -->
        <table class="presensi-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20px;">No</th>
                    <th rowspan="2" style="width: 150px;">Nama Lengkap & NIP</th>
                    <th rowspan="2" style="width: 60px;">Status</th>
                    <th colspan="{{ $jumlahHari }}">Tanggal Presensi Bulan {{ $namaBulan }}</th>
                    <th colspan="7">Total Rekapitulasi</th>
                    <th rowspan="2" style="width: 32px;">% Hadir</th>
                    <th rowspan="2" style="width: 42px;">Jam Kerja</th>
                    <th rowspan="2" style="width: 55px;">Paraf</th>
                </tr>
                <tr>
                    @for($d = 1; $d <= $jumlahHari; $d++)
                        <th style="width: 14px; {{ $hariInfo[$d]['is_libur'] ? 'background-color: #e5e7eb; color: #ef4444;' : '' }}">
                            {{ $d }}<br>
                            <span style="font-size: 5.5pt;">{{ $hariInfo[$d]['hari'] }}</span>
                        </th>
                    @endfor
                    <th style="width: 16px;" title="Hadir Tepat Waktu">H</th>
                    <th style="width: 16px;" title="Terlambat">T</th>
                    <th style="width: 16px;" title="Tugas Luar / Dinas Luar">DL</th>
                    <th style="width: 16px;" title="Sakit">S</th>
                    <th style="width: 16px;" title="Izin">I</th>
                    <th style="width: 16px;" title="Cuti">C</th>
                    <th style="width: 16px;" title="Alpa / Tanpa Keterangan">A</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $idx => $row)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $row['guru']->nama }}</strong>
                            <div style="font-size: 6.5pt; color: #4b5563;">
                                {{ $row['guru']->nip ? 'NIP. '.$row['guru']->nip : 'Non-NIP' }}
                            </div>
                        </td>
                        <td>{{ strtoupper($row['guru']->status_kepegawaian) }}</td>

                        @for($d = 1; $d <= $jumlahHari; $d++)
                            @php
                                $kd = $row['kehadiran'][$d]['kode'];
                                $isLibur = $row['kehadiran'][$d]['is_libur'];
                            @endphp
                            <td class="code-{{ $kd }} {{ $isLibur ? 'code-L' : '' }}">
                                {{ $kd !== '-' ? $kd : '' }}
                            </td>
                        @endfor

                        <td>{{ $row['total_hadir'] - $row['total_terlambat'] - $row['total_dinas_luar'] }}</td>
                        <td class="{{ $row['total_terlambat'] > 0 ? 'code-T' : '' }}">{{ $row['total_terlambat'] }}</td>
                        <td class="{{ $row['total_dinas_luar'] > 0 ? 'code-DL' : '' }}">{{ $row['total_dinas_luar'] }}</td>
                        <td class="{{ $row['total_sakit'] > 0 ? 'code-S' : '' }}">{{ $row['total_sakit'] }}</td>
                        <td class="{{ $row['total_izin'] > 0 ? 'code-I' : '' }}">{{ $row['total_izin'] }}</td>
                        <td class="{{ $row['total_cuti'] > 0 ? 'code-C' : '' }}">{{ $row['total_cuti'] }}</td>
                        <td class="{{ $row['total_alpa'] > 0 ? 'code-A' : '' }}">{{ $row['total_alpa'] }}</td>

                        <td><strong>{{ $row['persentase'] }}%</strong></td>
                        <td style="font-size: 6.5pt;">{{ $row['jam_kerja'] }}</td>
                        <td style="font-size: 6pt; color: #6b7280;">{{ $idx + 1 }}. .....</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $jumlahHari + 13 }}" style="padding: 15px;">Tidak ada data guru untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- KETERANGAN & LEGALITAS TTD -->
        <div class="footer-container">
            <div class="legend-box">
                <strong>Keterangan Simbol Kode:</strong>
                <strong>H</strong>: Hadir Tepat Waktu |
                <strong>T</strong>: Hadir Terlambat |
                <strong>DL</strong>: Tugas Luar / Dinas Luar (SPPD) |
                <strong>S</strong>: Sakit |
                <strong>I</strong>: Izin Dinas/Pribadi |
                <strong>C</strong>: Cuti Resmi |
                <strong>A</strong>: Tanpa Keterangan (Alpa) |
                <strong>L</strong>: Hari Libur / Akhir Pekan
            </div>

            <table class="ttd-table">
                <tr>
                    <td>
                        Mengetahui,<br>
                        Pengawas Pembina Sekolah,
                        <div class="ttd-space"></div>
                        <strong><u>( .............................................................. )</u></strong><br>
                        NIP. .....................................................
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
    </div>

    <!-- HALAMAN 2: SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK (SPTJB) -->
    <div class="sheet">
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
                    <div class="kop-instansi">PEMERINTAH KOTA / KABUPATEN - DINAS PENDIDIKAN</div>
                    <div class="kop-sekolah">{{ strtoupper($pengaturan->nama_sekolah) }}</div>
                    <div class="kop-alamat">
                        NPSN: {{ $pengaturan->npsn ?: '-' }} | Alamat: {{ $pengaturan->alamat ?: 'Alamat Sekolah Belum Diatur' }}<br>
                        Telp: {{ $pengaturan->telepon ?: '-' }} | Email: {{ $pengaturan->email ?: '-' }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="kop-divider"></div>

        <div class="sptjb-container" style="padding: 10px 40px;">
            <div class="sptjb-title">
                SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK (SPTJB)<br>
                KEBENARAN DATA PRESENSI GURU DAN TENAGA KEPENDIDIKAN
            </div>

            <p>Yang bertanda tangan di bawah ini:</p>

            <table style="width: 100%; margin-left: 20px; font-size: 10pt; line-height: 1.8;">
                <tr>
                    <td style="width: 200px;">Nama Lengkap</td>
                    <td>: <strong>{{ $pengaturan->kepala_sekolah ?: '-' }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>: {{ $pengaturan->nip_kepala_sekolah ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: Kepala Sekolah</td>
                </tr>
                <tr>
                    <td>Satuan Pendidikan</td>
                    <td>: {{ $pengaturan->nama_sekolah }}</td>
                </tr>
                <tr>
                    <td>Alamat Sekolah</td>
                    <td>: {{ $pengaturan->alamat ?: '-' }}</td>
                </tr>
            </table>

            <p style="text-align: justify; text-indent: 30px; margin-top: 15px;">
                Dengan ini menyatakan dengan sesungguhnya bahwa seluruh data rekapitulasi kehadiran, presensi mandiri berbasis GPS/kamera, izin, cuti, serta dinas luar bagi Guru dan Tenaga Kependidikan pada <strong>{{ $pengaturan->nama_sekolah }}</strong> untuk periode bulan <strong>{{ $namaBulan }} {{ $tahun }}</strong> sebagaimana terlampir pada lembar rekapitulasi presensi adalah <strong>BENAR, SAH, DAN DAPAT DIPERTANGGUNGJAWABKAN</strong> sesuai peraturan perundang-undangan yang berlaku.
            </p>

            <p style="text-align: justify; text-indent: 30px;">
                Apabila di kemudian hari ditemukan bukti adanya ketidakbenaran, rekayasa, atau manipulasi atas data kehadiran ini yang mengakibatkan kerugian keuangan daerah/negara (termasuk pembayaran Tunjangan Profesi Guru/TPG maupun Tambahan Penghasilan Pegawai/TPP), maka saya bersedia menerima sanksi administratif dan dituntut ganti rugi sesuai ketentuan hukum yang berlaku.
            </p>

            <p>Demikian Surat Pernyataan Tanggung Jawab Mutlak ini saya buat dengan penuh kesadaran dan rasa tanggung jawab.</p>

            <table style="width: 100%; margin-top: 35px; font-size: 10pt;">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; text-align: center;">
                        {{ $pengaturan->nama_sekolah ? explode(' ', $pengaturan->nama_sekolah)[0] : 'Makassar' }}, {{ now()->translatedFormat('d F Y') }}<br>
                        Kepala {{ $pengaturan->nama_sekolah }},
                        <div style="height: 15px;"></div>
                        <div style="border: 1px dashed #6b7280; width: 80px; height: 35px; margin: 0 auto; line-height: 35px; font-size: 7.5pt; color: #6b7280;">Materai Rp10.000</div>
                        <div style="height: 15px;"></div>
                        <strong><u>{{ $pengaturan->kepala_sekolah ?: '( Nama Kepala Sekolah Belum Diatur )' }}</u></strong><br>
                        NIP. {{ $pengaturan->nip_kepala_sekolah ?: '-' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
