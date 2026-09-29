<?php

namespace App\Filament\Guru\Pages;

use App\Models\Guru;
use App\Models\PengajuanIzin;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PengajuanIzinGuruPage extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\UnitEnum|null $navigationGroup = 'Layanan Guru';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'pengajuan-izin';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan Izin / Cuti';

    protected static ?string $title = 'Pengajuan Izin, Sakit & Cuti Mandiri';

    protected string $view = 'filament.guru.pages.pengajuan-izin-guru';

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();
        if (! $guru) {
            return null;
        }

        $count = PengajuanIzin::where('guru_id', $guru->id)
            ->where('status', 'menunggu')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pengajuan izin/cuti menunggu persetujuan';
    }

    public ?array $data = [];

    public ?Guru $guru = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->guru = $user?->guru ?? Guru::where('user_id', $user?->id)->first();

        $this->form->fill([
            'jenis' => 'izin',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Formulir Pengajuan Izin, Cuti & Tugas Luar')
                    ->description(fn () => 'Saldo Kuota Cuti Tahunan Anda saat ini: '.($this->guru?->sisa_cuti_tahun_ini ?? 12).' hari tersisa. Pengajuan akan ditinjau oleh Kepala Sekolah.')
                    ->schema([
                        Select::make('jenis')
                            ->label('Jenis Pengajuan')
                            ->options([
                                'sakit' => 'Sakit (Surat Keterangan Dokter)',
                                'izin' => 'Izin Dinas / Pribadi',
                                'cuti' => 'Cuti Tahunan / Melahirkan / Alasan Penting',
                                'dinas_luar' => 'Tugas Luar / Dinas Luar Sekolah (SPPD)',
                            ])
                            ->live()
                            ->required(),
                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false),
                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->afterOrEqual('tanggal_mulai')
                            ->native(false),
                        TextInput::make('lokasi_tugas')
                            ->label('Lokasi Kegiatan / Instansi Tujuan')
                            ->placeholder('Contoh: Hotel Claro Makassar / Kantor Dinas Pendidikan')
                            ->visible(fn ($get) => $get('jenis') === 'dinas_luar')
                            ->required(fn ($get) => $get('jenis') === 'dinas_luar'),
                        TextInput::make('nomor_surat_tugas')
                            ->label('Nomor Surat Tugas (SPPD)')
                            ->placeholder('Contoh: 800/123/DISDIK/IX/2026')
                            ->visible(fn ($get) => $get('jenis') === 'dinas_luar'),
                        Textarea::make('alasan')
                            ->label('Alasan / Uraian Keperluan Tugas')
                            ->placeholder('Jelaskan keperluan izin, agenda dinas luar, atau keterangan sakit...')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('lampiran')
                            ->label('Lampiran Bukti (Foto Surat Tugas / Surat Dokter / PDF Pendukung)')
                            ->disk('public')
                            ->directory('lampiran-izin')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function ajukan(): void
    {
        if (! $this->guru) {
            Notification::make()
                ->title('Akun login Anda belum terhubung ke data Guru.')
                ->danger()
                ->send();

            return;
        }

        $input = $this->form->getState();

        // Validasi kuota cuti tahunan
        if ($input['jenis'] === 'cuti') {
            $mulai = Carbon::parse($input['tanggal_mulai']);
            $selesai = Carbon::parse($input['tanggal_selesai']);
            $jumlahHari = $mulai->diffInDays($selesai) + 1;
            $sisaCuti = $this->guru->sisa_cuti_tahun_ini;

            if ($jumlahHari > $sisaCuti) {
                Notification::make()
                    ->title('Pengajuan Cuti Melebihi Kuota')
                    ->body("Pengajuan cuti Anda membutuhkan {$jumlahHari} hari, sedangkan sisa kuota cuti tahunan Anda hanya {$sisaCuti} hari.")
                    ->danger()
                    ->persistent()
                    ->send();

                return;
            }
        }

        PengajuanIzin::create([
            'guru_id' => $this->guru->id,
            'jenis' => $input['jenis'],
            'tanggal_mulai' => $input['tanggal_mulai'],
            'tanggal_selesai' => $input['tanggal_selesai'],
            'alasan' => $input['alasan'],
            'lokasi_tugas' => $input['lokasi_tugas'] ?? null,
            'nomor_surat_tugas' => $input['nomor_surat_tugas'] ?? null,
            'lampiran' => $input['lampiran'] ?? null,
            'status' => 'menunggu',
        ]);

        $this->form->fill([
            'jenis' => 'izin',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'alasan' => '',
            'lokasi_tugas' => '',
            'nomor_surat_tugas' => '',
            'lampiran' => null,
        ]);

        Notification::make()
            ->title('Pengajuan Berhasil Dikirim')
            ->body('Pengajuan Anda telah tercatat dan menunggu persetujuan dari Kepala Sekolah.')
            ->success()
            ->send();
    }

    public function batalkanPengajuan(int $id): void
    {
        if (! $this->guru) {
            return;
        }

        $izin = PengajuanIzin::where('id', $id)
            ->where('guru_id', $this->guru->id)
            ->where('status', 'menunggu')
            ->first();

        if ($izin) {
            $izin->delete();
            Notification::make()
                ->title('Pengajuan Telah Dibatalkan')
                ->body('Pengajuan izin Anda yang berstatus menunggu telah berhasil dihapus.')
                ->info()
                ->send();
        }
    }

    public function getRiwayatPengajuanProperty()
    {
        if (! $this->guru) {
            return collect();
        }

        return PengajuanIzin::where('guru_id', $this->guru->id)
            ->latest('created_at')
            ->get();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('ajukan')
                ->label('Kirim Pengajuan Izin')
                ->submit('ajukan')
                ->color('primary'),
        ];
    }
}
