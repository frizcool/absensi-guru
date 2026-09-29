<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use Carbon\Carbon;
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

class LaporanPresensiBulananExport
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
        public ?array $matriks = null,
    ) {
        $this->bulan = min(max($this->bulan, 1), 12);
        $this->tahun = max($this->tahun, 2020);
    }

    public function getMatriks(): array
    {
        if ($this->matriks !== null) {
            return $this->matriks;
        }

        $jumlahHari = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;

        $queryGuru = Guru::query()->with('shift')->where('aktif', true);

        if ($this->statusKepegawaian && $this->statusKepegawaian !== 'semua') {
            $queryGuru->where('status_kepegawaian', $this->statusKepegawaian);
        }

        if ($this->shiftId) {
            $queryGuru->where('shift_id', $this->shiftId);
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $queryGuru->where(function ($q) use ($searchTerm): void {
                $q->where('nama', 'like', $searchTerm)
                    ->orWhere('nip', 'like', $searchTerm)
                    ->orWhere('jabatan', 'like', $searchTerm);
            });
        }

        $gurus = $queryGuru->orderBy('nama', 'asc')->get();

        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->toDateString();
        $namaBulan = self::DAFTAR_BULAN[$this->bulan] ?? '';

        $presensis = Presensi::whereBetween('tanggal', [$startDate, $endDate])->get();

        $presensiMap = [];
        foreach ($presensis as $p) {
            $hari = (int) Carbon::parse($p->tanggal)->format('j');
            $presensiMap[$p->guru_id.'_'.$hari] = $p;
        }

        $hariInfo = [];
        for ($d = 1; $d <= $jumlahHari; $d++) {
            $tgl = Carbon::createFromDate($this->tahun, $this->bulan, $d);
            $isLibur = HariLibur::isLibur($tgl);
            $namaHari = $tgl->locale('id')->isoFormat('dd');
            $hariInfo[$d] = [
                'tanggal' => $tgl->toDateString(),
                'hari' => $namaHari,
                'is_libur' => $isLibur,
                'info_libur' => HariLibur::getInfoLibur($tgl),
            ];
        }

        $rows = [];
        $grandTotalHadir = 0;
        $grandTotalTerlambat = 0;
        $grandTotalDinasLuar = 0;
        $grandTotalSakit = 0;
        $grandTotalIzin = 0;
        $grandTotalCuti = 0;
        $grandTotalAlpa = 0;

        foreach ($gurus as $guru) {
            $kehadiranPerHari = [];
            $totalHadir = 0;
            $totalTerlambat = 0;
            $totalDinasLuar = 0;
            $totalSakit = 0;
            $totalIzin = 0;
            $totalCuti = 0;
            $totalAlpa = 0;

            for ($d = 1; $d <= $jumlahHari; $d++) {
                $key = $guru->id.'_'.$d;
                $p = $presensiMap[$key] ?? null;
                $libur = $hariInfo[$d]['is_libur'] || $guru->isLiburPadaTanggal($hariInfo[$d]['tanggal']);

                $kode = '-';

                if ($p) {
                    if ($p->status_kehadiran === 'hadir') {
                        if ($p->status_masuk === 'terlambat') {
                            $kode = 'T';
                            $totalTerlambat++;
                        } else {
                            $kode = 'H';
                        }
                        $totalHadir++;
                    } elseif ($p->status_kehadiran === 'dinas_luar') {
                        $kode = 'DL';
                        $totalDinasLuar++;
                        $totalHadir++;
                    } elseif ($p->status_kehadiran === 'sakit') {
                        $kode = 'S';
                        $totalSakit++;
                    } elseif ($p->status_kehadiran === 'izin') {
                        $kode = 'I';
                        $totalIzin++;
                    } elseif ($p->status_kehadiran === 'cuti') {
                        $kode = 'C';
                        $totalCuti++;
                    } elseif ($p->status_kehadiran === 'alpa') {
                        $kode = 'A';
                        $totalAlpa++;
                    }
                } elseif ($libur) {
                    $kode = 'L';
                }

                $kehadiranPerHari[$d] = [
                    'kode' => $kode,
                    'is_libur' => $libur,
                ];
            }

            $totalHariEfektif = count(array_filter($hariInfo, fn ($h) => ! $h['is_libur']));
            $persentase = $totalHariEfektif > 0 ? round(($totalHadir / $totalHariEfektif) * 100, 1) : 0;

            $grandTotalHadir += $totalHadir;
            $grandTotalTerlambat += $totalTerlambat;
            $grandTotalDinasLuar += $totalDinasLuar;
            $grandTotalSakit += $totalSakit;
            $grandTotalIzin += $totalIzin;
            $grandTotalCuti += $totalCuti;
            $grandTotalAlpa += $totalAlpa;

            $rows[] = [
                'guru' => $guru,
                'kehadiran' => $kehadiranPerHari,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_dinas_luar' => $totalDinasLuar,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_alpa' => $totalAlpa,
                'persentase' => $persentase,
            ];
        }

        $totalHariEfektif = count(array_filter($hariInfo, fn ($h) => ! $h['is_libur']));
        $avgPersentase = count($rows) > 0 ? round(array_sum(array_column($rows, 'persentase')) / count($rows), 1) : 0;

        return [
            'jumlah_hari' => $jumlahHari,
            'hari_info' => $hariInfo,
            'hari_efektif' => $totalHariEfektif,
            'rows' => $rows,
            'total_guru' => count($rows),
            'avg_persentase' => $avgPersentase,
            'grand_total_hadir' => $grandTotalHadir,
            'grand_total_terlambat' => $grandTotalTerlambat,
            'grand_total_dinas_luar' => $grandTotalDinasLuar,
            'grand_total_sakit' => $grandTotalSakit,
            'grand_total_izin' => $grandTotalIzin,
            'grand_total_cuti' => $grandTotalCuti,
            'grand_total_alpa' => $grandTotalAlpa,
            'periode_label' => $namaBulan.' '.$this->tahun,
        ];
    }

    public function exportToFile(string $filePath): void
    {
        $matriks = $this->getMatriks();
        $pengaturan = PengaturanSekolah::getSetting();
        $jumlahHari = (int) $matriks['jumlah_hari'];
        $totalCols = 4 + $jumlahHari + 8; // 4 info + N days + 8 summaries
        $lastColIndex = $totalCols - 1; // 0-indexed for mergeCells

        $options = new Options;

        // 1. Column Widths
        $options->setColumnWidth(6, 1);   // No
        $options->setColumnWidth(28, 2);  // Nama Guru
        $options->setColumnWidth(20, 3);  // NIP
        $options->setColumnWidth(14, 4);  // Status Pegawai

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $options->setColumnWidth(4.5, 4 + $d); // Day columns
        }

        $startSummaryCol = 5 + $jumlahHari;
        $options->setColumnWidth(5.5, $startSummaryCol);     // H
        $options->setColumnWidth(5.5, $startSummaryCol + 1); // T
        $options->setColumnWidth(5.5, $startSummaryCol + 2); // DL
        $options->setColumnWidth(5.5, $startSummaryCol + 3); // S
        $options->setColumnWidth(5.5, $startSummaryCol + 4); // I
        $options->setColumnWidth(5.5, $startSummaryCol + 5); // C
        $options->setColumnWidth(5.5, $startSummaryCol + 6); // A
        $options->setColumnWidth(13, $startSummaryCol + 7);  // % Kehadiran

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

        // Header Table Styles
        $thMainStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('0F766E')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder($borderThin);

        $thLiburStyle = (new Style)
            ->setFontBold()
            ->setFontSize(10)
            ->setFontName('Calibri')
            ->setFontColor(Color::WHITE)
            ->setBackgroundColor('DC2626')
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
            'H' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('15803D')->setBackgroundColor('DCFCE7')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'T' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('B45309')->setBackgroundColor('FEF3C7')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'DL' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('0369A1')->setBackgroundColor('E0F2FE')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'S' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('1D4ED8')->setBackgroundColor('DBEAFE')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'I' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('A16207')->setBackgroundColor('FEF9C3')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'C' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('7E22CE')->setBackgroundColor('F3E8FF')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'A' => (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('BE123C')->setBackgroundColor('FFE4E6')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            'L' => (new Style)->setFontSize(9)->setFontName('Calibri')->setFontColor('94A3B8')->setBackgroundColor('F1F5F9')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
            '-' => (new Style)->setFontSize(9)->setFontName('Calibri')->setFontColor('CBD5E1')->setCellAlignment(CellAlignment::CENTER)->setBorder($borderThin),
        ];

        // 1. KOP SURAT
        $writer->addRow(new Row([Cell::fromValue(strtoupper($pengaturan->nama_sekolah), $kopSekolahStyle)]));
        $writer->addRow(new Row([Cell::fromValue('Alamat: '.($pengaturan->alamat ?: '-').' | NPSN: '.($pengaturan->npsn ?: '-').' | Telp: '.($pengaturan->telepon ?: '-'), $kopAlamatStyle)]));
        $writer->addRow(new Row([Cell::fromValue('LAPORAN REKAPITULASI PRESENSI GURU & TENAGA KEPENDIDIKAN', $kopInstansiStyle)]));

        $filterLabel = 'Periode: '.$matriks['periode_label'];
        if ($this->statusKepegawaian && $this->statusKepegawaian !== 'semua') {
            $filterLabel .= ' | Status: '.strtoupper($this->statusKepegawaian);
        }
        if ($this->shiftId && ($shift = Shift::find($this->shiftId))) {
            $filterLabel .= ' | Shift: '.$shift->nama;
        }
        $filterLabel .= ' | Hari Efektif: '.$matriks['hari_efektif'].' Hari | Total Terdata: '.$matriks['total_guru'].' Guru';
        $writer->addRow(new Row([Cell::fromValue($filterLabel, $kopPeriodeStyle)]));

        // Blank Row 5
        $writer->addRow(new Row([]));

        // 2. HEADER TABEL
        $headerCells = [
            Cell::fromValue('No', $thMainStyle),
            Cell::fromValue('Nama Guru / Pegawai', $thMainStyle),
            Cell::fromValue('NIP', $thMainStyle),
            Cell::fromValue('Status', $thMainStyle),
        ];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $isLibur = $matriks['hari_info'][$d]['is_libur'];
            $headerCells[] = Cell::fromValue((string) $d, $isLibur ? $thLiburStyle : $thMainStyle);
        }

        $headerCells[] = Cell::fromValue('H', $thMainStyle);
        $headerCells[] = Cell::fromValue('T', $thMainStyle);
        $headerCells[] = Cell::fromValue('DL', $thMainStyle);
        $headerCells[] = Cell::fromValue('S', $thMainStyle);
        $headerCells[] = Cell::fromValue('I', $thMainStyle);
        $headerCells[] = Cell::fromValue('C', $thMainStyle);
        $headerCells[] = Cell::fromValue('A', $thMainStyle);
        $headerCells[] = Cell::fromValue('% Kehadiran', $thMainStyle);

        $writer->addRow(new Row($headerCells));

        // 3. DATA ROWS
        foreach ($matriks['rows'] as $index => $row) {
            $dataCells = [
                Cell::fromValue($index + 1, $cellCenterStyle),
                Cell::fromValue($row['guru']->nama, $cellLeftStyle),
                Cell::fromValue($row['guru']->nip ?: '-', $cellCenterStyle),
                Cell::fromValue(strtoupper($row['guru']->status_kepegawaian), $cellCenterStyle),
            ];

            for ($d = 1; $d <= $jumlahHari; $d++) {
                $kode = $row['kehadiran'][$d]['kode'];
                $style = $statusStyles[$kode] ?? $cellCenterStyle;
                $dataCells[] = Cell::fromValue($kode, $style);
            }

            $dataCells[] = Cell::fromValue($row['total_hadir'], $cellBoldCenter);
            $dataCells[] = Cell::fromValue($row['total_terlambat'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['total_dinas_luar'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['total_sakit'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['total_izin'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['total_cuti'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['total_alpa'], $cellCenterStyle);
            $dataCells[] = Cell::fromValue($row['persentase'].'%', $cellBoldCenter);

            $writer->addRow(new Row($dataCells));
        }

        // 4. TOTAL REKAPITULASI ROW
        $totalStyle = (new Style)
            ->setFontBold()
            ->setFontSize(9.5)
            ->setFontName('Calibri')
            ->setFontColor('0F172A')
            ->setBackgroundColor('F1F5F9')
            ->setCellAlignment(CellAlignment::CENTER)
            ->setBorder($borderThin);

        $totalCells = [
            Cell::fromValue('TOTAL REKAPITULASI', $totalStyle),
            Cell::fromValue('', $totalStyle),
            Cell::fromValue('', $totalStyle),
            Cell::fromValue('', $totalStyle),
        ];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $totalCells[] = Cell::fromValue('-', $totalStyle);
        }

        $totalCells[] = Cell::fromValue($matriks['grand_total_hadir'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_terlambat'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_dinas_luar'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_sakit'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_izin'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_cuti'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['grand_total_alpa'], $totalStyle);
        $totalCells[] = Cell::fromValue($matriks['avg_persentase'].'%', $totalStyle);

        $writer->addRow(new Row($totalCells));

        // 5. LEGENDA KETERANGAN
        $writer->addRow(new Row([])); // Blank
        $legendTitleStyle = (new Style)->setFontBold()->setFontSize(9)->setFontName('Calibri')->setFontColor('334155');
        $legendDescStyle = (new Style)->setFontSize(8.5)->setFontName('Calibri')->setFontColor('64748B');

        $writer->addRow(new Row([Cell::fromValue('KETERANGAN SIMBOL PRESENSI:', $legendTitleStyle)]));
        $writer->addRow(new Row([Cell::fromValue('H = Hadir Tepat Waktu | T = Terlambat | DL = Tugas Luar / Dinas Luar | S = Sakit | I = Izin | C = Cuti | A = Alpa | L = Libur / Akhir Pekan', $legendDescStyle)]));

        // 6. TANDA TANGAN RESMI
        $writer->addRow(new Row([])); // Blank
        $ttdTitleStyle = (new Style)->setFontSize(9.5)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);
        $ttdBoldStyle = (new Style)->setFontBold()->setFontSize(10)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);
        $ttdNoteStyle = (new Style)->setFontSize(9)->setFontName('Calibri')->setCellAlignment(CellAlignment::CENTER);

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Baris Tanggal & Mengetahui
        $ttdRow1 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow1[1] = Cell::fromValue('Mengetahui,', $ttdTitleStyle);
        $ttdRow1[$startSummaryCol] = Cell::fromValue('Makassar, '.$tanggalCetak, $ttdTitleStyle);
        $writer->addRow(new Row($ttdRow1));

        // Baris Jabatan
        $ttdRow2 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow2[1] = Cell::fromValue('Kepala Sekolah', $ttdBoldStyle);
        $ttdRow2[$startSummaryCol] = Cell::fromValue('Petugas Presensi', $ttdBoldStyle);
        $writer->addRow(new Row($ttdRow2));

        // Spasi TTD
        $writer->addRow(new Row([]));
        $writer->addRow(new Row([]));

        // Baris Nama Pejabat
        $namaKepsek = $pengaturan->kepala_sekolah ?: '..........................................';
        $nipKepsek = $pengaturan->nip_kepala_sekolah ? 'NIP. '.$pengaturan->nip_kepala_sekolah : 'NIP. -';

        $ttdRow3 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow3[1] = Cell::fromValue($namaKepsek, $ttdBoldStyle);
        $ttdRow3[$startSummaryCol] = Cell::fromValue(auth()->user()?->name ?: 'Administrator', $ttdBoldStyle);
        $writer->addRow(new Row($ttdRow3));

        // Baris NIP
        $ttdRow4 = array_fill(0, $totalCols, Cell::fromValue(''));
        $ttdRow4[1] = Cell::fromValue($nipKepsek, $ttdNoteStyle);
        $ttdRow4[$startSummaryCol] = Cell::fromValue('NIP. -', $ttdNoteStyle);
        $writer->addRow(new Row($ttdRow4));

        $writer->close();
    }

    public function download(?string $filename = null): BinaryFileResponse
    {
        $namaBulan = self::DAFTAR_BULAN[$this->bulan] ?? 'Bulan';
        $filename = $filename ?: "Rekap_Presensi_Guru_{$namaBulan}_{$this->tahun}.xlsx";
        $tempPath = storage_path('app/temp_'.uniqid().'_'.$filename);

        $this->exportToFile($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
