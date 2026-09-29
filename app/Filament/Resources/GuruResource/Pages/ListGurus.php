<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Models\Guru;
use App\Models\Shift;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListGurus extends ListRecords
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Unduh Template Import')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->downloadTemplateCsv()),

            Action::make('importDariSk')
                ->label('Import Data Guru (CSV / Excel)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->modalHeading('Import Data Guru & Tendik')
                ->modalDescription('Unggah file CSV atau Excel (.xlsx) sesuai format template. Sistem akan otomatis mendaftarkan data guru dan membuat akun login.')
                ->form([
                    FileUpload::make('file')
                        ->label('Pilih File (.csv / .xlsx)')
                        ->required()
                        ->disk('local')
                        ->directory('import-guru')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->prosesImportFile($data['file']);
                }),

            CreateAction::make()->label('Tambah Guru Baru'),
        ];
    }

    protected function downloadTemplateCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_guru.csv"',
        ];

        $columns = ['nama', 'nip', 'nuptk', 'jenis_kelamin', 'status_kepegawaian', 'pangkat_golongan', 'jabatan', 'jenis_guru', 'no_hp', 'jumlah_jam'];
        $contoh1 = ['Ahmad Fauzi, S.Pd', '198501012010011001', '1234567890123456', 'L', 'pns', 'Penata Muda / III.a', 'Guru Kelas', 'Guru Kelas IV', '081234567890', '24'];
        $contoh2 = ['Nurul Hidayah, S.Pd', '', '9876543210123456', 'P', 'non_pns', '-', 'Guru Mata Pelajaran', 'Guru PJOK', '085234567891', '18'];

        return response()->stream(function () use ($columns, $contoh1, $contoh2) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 agar Excel membaca aksen dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $columns);
            fputcsv($handle, $contoh1);
            fputcsv($handle, $contoh2);
            fclose($handle);
        }, 200, $headers);
    }

    protected function prosesImportFile(string $filePath): void
    {
        $fullPath = Storage::disk('local')->path($filePath);

        if (! file_exists($fullPath)) {
            Notification::make()->title('File import tidak ditemukan')->danger()->send();

            return;
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $defaultShiftId = Shift::first()?->id;

        $reader = ($extension === 'xlsx') ? new XlsxReader : new CsvReader;

        try {
            $reader->open($fullPath);

            $headerMap = [];
            $totalSuccess = 0;
            $rowIndex = 0;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->toArray();
                    $rowIndex++;

                    if ($rowIndex === 1) {
                        // Petakan indeks kolom header
                        foreach ($cells as $idx => $headerName) {
                            $headerKey = strtolower(trim((string) $headerName));
                            $headerMap[$headerKey] = $idx;
                        }

                        continue;
                    }

                    // Ambil nilai kolom
                    $nama = isset($headerMap['nama']) ? trim((string) ($cells[$headerMap['nama']] ?? '')) : '';
                    if (empty($nama)) {
                        continue;
                    }

                    $nip = isset($headerMap['nip']) ? trim((string) ($cells[$headerMap['nip']] ?? '')) : null;
                    $nuptk = isset($headerMap['nuptk']) ? trim((string) ($cells[$headerMap['nuptk']] ?? '')) : null;
                    $jk = isset($headerMap['jenis_kelamin']) ? strtoupper(trim((string) ($cells[$headerMap['jenis_kelamin']] ?? 'L'))) : 'L';
                    $status = isset($headerMap['status_kepegawaian']) ? strtolower(trim((string) ($cells[$headerMap['status_kepegawaian']] ?? 'non_pns'))) : 'non_pns';
                    $pangkat = isset($headerMap['pangkat_golongan']) ? trim((string) ($cells[$headerMap['pangkat_golongan']] ?? '')) : null;
                    $jabatan = isset($headerMap['jabatan']) ? trim((string) ($cells[$headerMap['jabatan']] ?? 'Guru')) : 'Guru';
                    $jenisGuru = isset($headerMap['jenis_guru']) ? trim((string) ($cells[$headerMap['jenis_guru']] ?? '')) : null;
                    $noHp = isset($headerMap['no_hp']) ? trim((string) ($cells[$headerMap['no_hp']] ?? '')) : null;
                    $jam = isset($headerMap['jumlah_jam']) ? (int) ($cells[$headerMap['jumlah_jam']] ?? 24) : 24;

                    if (! in_array($status, ['pns', 'pppk', 'non_pns'])) {
                        $status = 'non_pns';
                    }
                    if (! in_array($jk, ['L', 'P'])) {
                        $jk = 'L';
                    }

                    $guru = null;
                    if (! empty($nip)) {
                        $guru = Guru::where('nip', $nip)->first();
                    }

                    if (! $guru) {
                        $guru = Guru::where('nama', $nama)->first();
                    }

                    $guruData = [
                        'nama' => $nama,
                        'nip' => ! empty($nip) ? $nip : null,
                        'nuptk' => ! empty($nuptk) ? $nuptk : null,
                        'jenis_kelamin' => $jk,
                        'status_kepegawaian' => $status,
                        'pangkat_golongan' => $pangkat,
                        'jabatan' => $jabatan,
                        'jenis_guru' => $jenisGuru,
                        'no_hp' => $noHp,
                        'jumlah_jam' => $jam,
                        'aktif' => true,
                    ];

                    if ($guru) {
                        $guru->update($guruData);
                    } else {
                        $guruData['shift_id'] = $defaultShiftId;
                        $guru = Guru::create($guruData);
                    }

                    // Otomatis buat / sinkronkan akun user login
                    $guru->ensureUserAccountExists();
                    $totalSuccess++;
                }
                break; // Proses lembar pertama
            }

            $reader->close();
            @unlink($fullPath);

            Notification::make()
                ->title('Import Data Guru Berhasil')
                ->body("Berhasil mengimpor dan memperbarui {$totalSuccess} data guru beserta akun login pengguna.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Membaca File Import')
                ->body('Terjadi kesalahan: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
