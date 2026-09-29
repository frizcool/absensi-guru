<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanRincianPresensiExport
{
    protected const DAFTAR_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function __construct(
        public int $bulan,
        public int $tahun,
        public ?string $statusKepegawaian = 'semua',
        public ?int $shiftId = null,
        public string $search = '',
    ) {
        $this->bulan = min(max($this->bulan, 1), 12);
        $this->tahun = max($this->tahun, 2020);
    }

    public function getQuery(): Builder
    {
        $status = in_array($this->statusKepegawaian, ['pns', 'pppk', 'non_pns'], true)
            ? $this->statusKepegawaian
            : null;
        $search = mb_substr(trim($this->search), 0, 100);
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->toDateString();

        return Presensi::query()
            ->with(['guru', 'shift'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->whereHas('guru', function (Builder $query) use ($status, $search): void {
                $query->where('aktif', true);

                if ($status) {
                    $query->where('status_kepegawaian', $status);
                }

                if ($this->shiftId) {
                    $query->where('shift_id', $this->shiftId);
                }

                if ($search !== '') {
                    $searchTerm = '%'.$search.'%';
                    $query->where(function (Builder $query) use ($searchTerm): void {
                        $query->where('nama', 'like', $searchTerm)
                            ->orWhere('nip', 'like', $searchTerm)
                            ->orWhere('nuptk', 'like', $searchTerm)
                            ->orWhere('jabatan', 'like', $searchTerm);
                    });
                }
            })
            ->orderBy('tanggal')
            ->orderBy('guru_id');
    }

    public function exportToFile(string $filePath): void
    {
        $records = $this->getQuery()->get();
        $pengaturan = PengaturanSekolah::getSetting();
        $namaBulan = self::DAFTAR_BULAN[$this->bulan] ?? 'Bulan';
        $shift = $this->shiftId ? Shift::find($this->shiftId) : null;

        $totalCols = 14;
        $lastColIndex = $totalCols - 1;

        $options = new Options;

        // 1. Column Widths
        $options->setColumnWidth(6, 1);   // No
        $options->setColumnWidth(18, 2);  // Hari & Tanggal
        $options->setColumnWidth(28, 3);  // Nama Guru
        $options->setColumnWidth(20, 4);  // NIP
        $options->setColumnWidth(14, 5);  // Status Pegawai
        $options->setColumnWidth(20, 6);  // Shift
        $options->setColumnWidth(13, 7);  // Jam Masuk
        $options->setColumnWidth(15, 8);  // Status Masuk
        $options->setColumnWidth(13, 9);  // Jam Pulang
        $options->setColumnWidth(15, 10); // Status Pulang
        $options->setColumnWidth(13, 11); // Durasi Kerja
        $options->setColumnWidth(16, 12); // Status Kehadiran
        $options->setColumnWidth(22, 13); // Koordinat / Lokasi
        $options->setColumnWidth(30, 14); // Keterangan

        // 2. Merged Header Rows (KOP Surat)
        $options->mergeCells(0, 1, $lastColIndex, 1);
        $options->mergeCells(0, 2, $lastColIndex, 2);
        $options->mergeCells(0, 3, $lastColIndex, 3);
        $options->mergeCells(0, 4, $lastColIndex, 4);

        $writer = new Writer($options);
        $writer->openToFile($filePath);

        // STYLES DEFINITION
        $borderThin = new Border(
            new BorderPart(Border::TOP, 'D1D5DB', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, 'D1D5DB', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::LEFT, 'D1D5DB', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, 'D1D5DB', Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        $kopInstansiStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setFontName('Calibri')
            ->setFontColor('334155')
            ->setCellAlignment(CellAlignment::CENTER);

        $kopSekolahStyle = (new Style)
            ->setFontBold()
            ->setFontSize(14)
            ->setFontName('Calibri')
            ->setFontColor('0F172A')
            ->setCellAlignment(CellAlignment::CENTER);

        $kopAlamatStyle = (new Style)
            ->setFontSize(9)
            ->setFontItalic()
            ->setFontName('Calibri')
            ->setFontColor('64748B')
            ->setCellAlignment(CellAlignment::CENTER);

        $kopPeriodeStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setFontColor('0F766E')
            ->setCellAlignment(CellAlignment::CENTER);

        // Header Table Style
        $thStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('0F766E')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($borderThin);

        // Data Styles
        $cellCenterStyle = (new Style)
            ->setFontSize(9.5)
            ->setFontName('Calibri')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($borderThin);

        $cellLeftStyle = (new Style)
            ->setFontSize(9.5)
            ->setFontName('Calibri')
            ->setCellAlignment(CellAlignment::LEFT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($borderThin);

        $cellBoldCenter = (new Style)
            ->setFontBold()
            ->setFontSize(9.5)
            ->setFontName('Calibri')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($borderThin);

        // Badge Status Styles
        $statusStyles = [
            'hadir' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('15803D')->setBackgroundColor('DCFCE7')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'dinas_luar' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('0369A1')->setBackgroundColor('E0F2FE')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'sakit' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('1D4ED8')->setBackgroundColor('DBEAFE')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'izin' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('A16207')->setBackgroundColor('FEF9C3')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'cuti' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('7E22CE')->setBackgroundColor('F3E8FF')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'alpa' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('BE123C')->setBackgroundColor('FFE4E6')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
        ];

        $masukTepatStyle = (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('15803D')->setBackgroundColor('DCFCE7')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin);
        $masukTerlambatStyle = (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('B45309')->setBackgroundColor('FEF3C7')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin);

        // 1. KOP SURAT
        $writer->addRow(new Row([Cell::fromValue(strtoupper($pengaturan->nama_sekolah), $kopSekolahStyle)]));
        $writer->addRow(new Row([Cell::fromValue('Alamat: '.($pengaturan->alamat ?: '-').' | NPSN: '.($pengaturan->npsn ?: '-').' | Telp: '.($pengaturan->telepon ?: '-'), $kopAlamatStyle)]));
        $writer->addRow(new Row([Cell::fromValue('LAPORAN RINCIAN PRESENSI & JAM KERJA HARIAN GURU', $kopInstansiStyle)]));

        $filterLabel = "Periode: {$namaBulan} {$this->tahun}";
        if ($this->statusKepegawaian && $this->statusKepegawaian !== 'semua') {
            $filterLabel .= ' | Status: '.strtoupper(str_replace('_', ' ', $this->statusKepegawaian));
        }
        if ($shift) {
            $filterLabel .= ' | Shift: '.$shift->nama;
        }
        if ($this->search !== '') {
            $filterLabel .= ' | Pencarian: "'.$this->search.'"';
        }
        $filterLabel .= ' | Total Terdata: '.$records->count().' Presensi';
        $writer->addRow(new Row([Cell::fromValue($filterLabel, $kopPeriodeStyle)]));

        // Blank Row 5
        $writer->addRow(new Row([]));

        // 2. HEADER TABEL
        $headers = [
            Cell::fromValue('No', $thStyle),
            Cell::fromValue('Hari & Tanggal', $thStyle),
            Cell::fromValue('Nama Guru / Pegawai', $thStyle),
            Cell::fromValue('NIP', $thStyle),
            Cell::fromValue('Status Pegawai', $thStyle),
            Cell::fromValue('Shift Kerja', $thStyle),
            Cell::fromValue('Jam Masuk', $thStyle),
            Cell::fromValue('Status Masuk', $thStyle),
            Cell::fromValue('Jam Pulang', $thStyle),
            Cell::fromValue('Status Pulang', $thStyle),
            Cell::fromValue('Durasi Kerja', $thStyle),
            Cell::fromValue('Status Kehadiran', $thStyle),
            Cell::fromValue('Koordinat Lokasi', $thStyle),
            Cell::fromValue('Keterangan', $thStyle),
        ];
        $writer->addRow(new Row($headers));

        // Counters for summary
        $totalHadirTepat = 0;
        $totalTerlambat = 0;
        $totalDinasLuar = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalCuti = 0;
        $totalAlpa = 0;

        // 3. DATA ROWS
        foreach ($records as $index => $r) {
            $tgl = Carbon::parse($r->tanggal);
            $hariTanggal = $tgl->locale('id')->isoFormat('dddd, DD/MM/Y');

            $jamMasuk = $r->jam_masuk ? Carbon::parse($r->jam_masuk)->format('H:i:s') : '-';
            $jamPulang = $r->jam_pulang ? Carbon::parse($r->jam_pulang)->format('H:i:s') : '-';

            $statusMasukLabel = match ($r->status_masuk) {
                'tepat_waktu' => 'Tepat Waktu',
                'terlambat' => 'Terlambat',
                default => '-',
            };

            $statusMasukStyle = match ($r->status_masuk) {
                'tepat_waktu' => $masukTepatStyle,
                'terlambat' => $masukTerlambatStyle,
                default => $cellCenterStyle,
            };

            $statusPulangLabel = match ($r->status_pulang) {
                'normal' => 'Normal',
                'pulang_cepat' => 'Pulang Cepat',
                default => '-',
            };

            $durasiKerja = '-';
            if ($r->jam_masuk && $r->jam_pulang) {
                $diffMenit = abs((int) Carbon::parse($r->jam_masuk)->diffInMinutes(Carbon::parse($r->jam_pulang)));
                $durasiKerja = floor($diffMenit / 60).'j '.($diffMenit % 60).'m';
            }

            $statusKehadiranLabel = match ($r->status_kehadiran) {
                'hadir' => ($r->status_masuk === 'terlambat' ? 'Terlambat' : 'Hadir Tepat Waktu'),
                'dinas_luar' => 'Dinas Luar',
                'sakit' => 'Sakit',
                'izin' => 'Izin',
                'cuti' => 'Cuti',
                'alpa' => 'Alpa',
                default => ucfirst(str_replace('_', ' ', $r->status_kehadiran ?? '-')),
            };

            $statusKehadiranStyle = $statusStyles[$r->status_kehadiran] ?? $cellCenterStyle;

            // Increment stats
            if ($r->status_kehadiran === 'hadir') {
                if ($r->status_masuk === 'terlambat') {
                    $totalTerlambat++;
                } else {
                    $totalHadirTepat++;
                }
            } elseif ($r->status_kehadiran === 'dinas_luar') {
                $totalDinasLuar++;
            } elseif ($r->status_kehadiran === 'sakit') {
                $totalSakit++;
            } elseif ($r->status_kehadiran === 'izin') {
                $totalIzin++;
            } elseif ($r->status_kehadiran === 'cuti') {
                $totalCuti++;
            } elseif ($r->status_kehadiran === 'alpa') {
                $totalAlpa++;
            }

            $lokasiText = '-';
            if ($r->lokasi_masuk_lat && $r->lokasi_masuk_lng) {
                $lokasiText = number_format($r->lokasi_masuk_lat, 5).', '.number_format($r->lokasi_masuk_lng, 5);
            }

            $shiftText = $r->shift
                ? $r->shift->nama.' ('.Carbon::parse($r->shift->jam_masuk)->format('H:i').'-'.Carbon::parse($r->shift->jam_pulang)->format('H:i').')'
                : '-';

            $dataRow = [
                Cell::fromValue($index + 1, $cellCenterStyle),
                Cell::fromValue($hariTanggal, $cellCenterStyle),
                Cell::fromValue($r->guru?->nama ?: '-', $cellLeftStyle),
                Cell::fromValue($r->guru?->nip ?: '-', $cellCenterStyle),
                Cell::fromValue(strtoupper(str_replace('_', ' ', $r->guru?->status_kepegawaian ?? '-')), $cellCenterStyle),
                Cell::fromValue($shiftText, $cellLeftStyle),
                Cell::fromValue($jamMasuk, $cellCenterStyle),
                Cell::fromValue($statusMasukLabel, $statusMasukStyle),
                Cell::fromValue($jamPulang, $cellCenterStyle),
                Cell::fromValue($statusPulangLabel, $cellCenterStyle),
                Cell::fromValue($durasiKerja, $cellBoldCenter),
                Cell::fromValue($statusKehadiranLabel, $statusKehadiranStyle),
                Cell::fromValue($lokasiText, $cellCenterStyle),
                Cell::fromValue($r->keterangan ?: '-', $cellLeftStyle),
            ];

            $writer->addRow(new Row($dataRow));
        }

        // 4. STATISTIK RINGKASAN
        $writer->addRow(new Row([])); // Blank row

        $boxHeaderStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('1E293B')
            ->setCellAlignment(CellAlignment::LEFT)
            ->setBorder($borderThin);

        $boxLabelStyle = (new Style)
            ->setFontBold()
            ->setFontSize(9.5)
            ->setFontName('Calibri')
            ->setBackgroundColor('F8FAFC')
            ->setCellAlignment(CellAlignment::LEFT)
            ->setBorder($borderThin);

        $boxValStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setBorder($borderThin);

        $writer->addRow(new Row([
            Cell::fromValue('RINGKASAN STATISTIK PRESENSI', $boxHeaderStyle),
            Cell::fromValue('JUMLAH', $boxHeaderStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Total Rekaman Presensi', $boxLabelStyle),
            Cell::fromValue($records->count(), $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Hadir Tepat Waktu', $boxLabelStyle),
            Cell::fromValue($totalHadirTepat, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Hadir Terlambat', $boxLabelStyle),
            Cell::fromValue($totalTerlambat, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Tugas Luar / Dinas Luar', $boxLabelStyle),
            Cell::fromValue($totalDinasLuar, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Sakit (Surat Dokter / Izin)', $boxLabelStyle),
            Cell::fromValue($totalSakit, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Izin Resmi', $boxLabelStyle),
            Cell::fromValue($totalIzin, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Cuti', $boxLabelStyle),
            Cell::fromValue($totalCuti, $boxValStyle),
        ]));

        $writer->addRow(new Row([
            Cell::fromValue('Alpa / Tanpa Keterangan', $boxLabelStyle),
            Cell::fromValue($totalAlpa, $boxValStyle),
        ]));

        // 5. TANDA TANGAN KEDINASAN
        $writer->addRow(new Row([])); // Blank
        $ttdTitleStyle = (new Style)->setFontSize(9.5)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);
        $ttdBoldStyle = (new Style)->setFontBold()->setFontSize(10)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);
        $ttdNoteStyle = (new Style)->setFontSize(9)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Baris Tanggal & Mengetahui
        $ttdRow1 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow1[2] = Cell::fromValue('Mengetahui,', $ttdTitleStyle);
        $ttdRow1[8] = Cell::fromValue('Makassar, '.$tanggalCetak, $ttdTitleStyle);
        $writer->addRow(new Row($ttdRow1));

        // Baris Jabatan
        $ttdRow2 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow2[2] = Cell::fromValue('Kepala Sekolah', $ttdBoldStyle);
        $ttdRow2[8] = Cell::fromValue('Petugas Presensi', $ttdBoldStyle);
        $writer->addRow(new Row($ttdRow2));

        // Spasi TTD
        $writer->addRow(new Row([]));
        $writer->addRow(new Row([]));

        // Baris Nama Pejabat
        $namaKepsek = $pengaturan->kepala_sekolah ?: '..........................................';
        $nipKepsek = $pengaturan->nip_kepala_sekolah ? 'NIP. '.$pengaturan->nip_kepala_sekolah : 'NIP. -';

        $ttdRow3 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow3[2] = Cell::fromValue($namaKepsek, $ttdBoldStyle);
        $ttdRow3[8] = Cell::fromValue(auth()->user()?->name ?: 'Administrator', $ttdBoldStyle);
        $writer->addRow(new Row($ttdRow3));

        // Baris NIP
        $ttdRow4 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow4[2] = Cell::fromValue($nipKepsek, $ttdNoteStyle);
        $ttdRow4[8] = Cell::fromValue('NIP. -', $ttdNoteStyle);
        $writer->addRow(new Row($ttdRow4));

        $writer->close();
    }

    public function download(?string $filename = null): BinaryFileResponse
    {
        $namaBulan = self::DAFTAR_BULAN[$this->bulan] ?? 'Bulan';
        $filename = $filename ?: "Rincian_Presensi_Guru_{$namaBulan}_{$this->tahun}.xlsx";
        $tempPath = storage_path('app/temp_'.uniqid().'_'.$filename);

        $this->exportToFile($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
