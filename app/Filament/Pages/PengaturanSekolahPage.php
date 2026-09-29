<?php

namespace App\Filament\Pages;

use App\Models\PengaturanSekolah;
use App\Services\WhatsAppService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class PengaturanSekolahPage extends Page implements HasSchemas
{
    use HasPageShield, InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Profil, Radius & WhatsApp';

    protected static ?string $title = 'Pengaturan Sekolah, Geofencing & Notifikasi WhatsApp';

    protected string $view = 'filament.pages.pengaturan-sekolah';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = PengaturanSekolah::getSetting();
        $this->form->fill($setting->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Tabs::make('Pengaturan')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Lokasi & Geofencing (GPS)')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Section::make('Radius & Validasi Lokasi Presensi')
                                    ->description('Tentukan titik koordinat pusat sekolah dan jarak toleransi radius presensi guru.')
                                    ->schema([
                                        FormComponents\Toggle::make('wajib_validasi_lokasi')
                                            ->label('Aktifkan Validasi Lokasi (Geofencing GPS)')
                                            ->helperText('Jika aktif, guru WAJIB berada di dalam radius sekolah saat melakukan check-in dan check-out.')
                                            ->default(false)
                                            ->columnSpanFull(),

                                        FormComponents\TextInput::make('latitude')
                                            ->label('Latitude Sekolah')
                                            ->placeholder('-5.147665')
                                            ->numeric()
                                            ->helperText('Gunakan tombol "Ambil Lokasi Saya" di bawah atau salin dari Google Maps.')
                                            ->required(fn ($get) => $get('wajib_validasi_lokasi'))
                                            ->live(onBlur: true),

                                        FormComponents\TextInput::make('longitude')
                                            ->label('Longitude Sekolah')
                                            ->placeholder('119.432731')
                                            ->numeric()
                                            ->required(fn ($get) => $get('wajib_validasi_lokasi'))
                                            ->live(onBlur: true),

                                        FormComponents\TextInput::make('radius_meter')
                                            ->label('Radius Jangkauan Presensi (Meter)')
                                            ->numeric()
                                            ->default(100)
                                            ->minValue(10)
                                            ->maxValue(5000)
                                            ->suffix('meter')
                                            ->helperText('Jarak maksimal guru dari titik koordinat sekolah (Disarankan: 50 - 150 meter).')
                                            ->required()
                                            ->live(onBlur: true),

                                        FormComponents\TextInput::make('maksimal_akurasi_gps')
                                            ->label('Batas Toleransi Akurasi Sinyal GPS')
                                            ->numeric()
                                            ->default(150)
                                            ->minValue(20)
                                            ->maxValue(1000)
                                            ->suffix('meter')
                                            ->helperText('Mencegah Fake GPS atau sinyal sangat lemah. Jika browser melaporkan deviasi akurasi di atas angka ini, presensi ditolak.'),

                                        FormComponents\Toggle::make('wajib_device_binding')
                                            ->label('Aktifkan Kunci Perangkat (Device Binding - 1 Akun 1 HP)')
                                            ->helperText('Jika aktif, 1 guru hanya dapat presensi dari 1 perangkat HP yang telah terdaftar saat pertama kali absen. Mencegah titip absen antar-guru.')
                                            ->default(false)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Notifikasi WhatsApp')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('Notifikasi Otomatis ke WhatsApp Kepala Sekolah')
                                    ->description('Kirim pesan WhatsApp otomatis ke Kepala Sekolah saat terdeteksi keterlambatan guru berulang.')
                                    ->schema([
                                        FormComponents\Toggle::make('notif_terlambat_aktif')
                                            ->label('Aktifkan Notifikasi WhatsApp Keterlambatan')
                                            ->helperText('Otomatis mengirim WhatsApp ke Kepala Sekolah jika guru mencapai batas keterlambatan.')
                                            ->columnSpanFull(),

                                        FormComponents\TextInput::make('no_wa_kepala_sekolah')
                                            ->label('No. WhatsApp Kepala Sekolah')
                                            ->placeholder('Contoh: 081234567890')
                                            ->tel()
                                            ->required(fn ($get) => $get('notif_terlambat_aktif')),

                                        FormComponents\TextInput::make('ambang_keterlambatan')
                                            ->label('Batas Frekuensi Terlambat (per Bulan)')
                                            ->numeric()
                                            ->default(3)
                                            ->minValue(1)
                                            ->maxValue(30)
                                            ->suffix('kali')
                                            ->helperText('Kirim notifikasi jika guru terlambat mencapai atau melebihi jumlah ini dalam 1 bulan.')
                                            ->required(fn ($get) => $get('notif_terlambat_aktif')),

                                        FormComponents\Select::make('wa_provider')
                                            ->label('Provider WhatsApp Gateway')
                                            ->options([
                                                'fonnte' => 'Fonnte (fonnte.com)',
                                                'wablas' => 'Wablas (wablas.com)',
                                                'generic' => 'Custom Webhook / Generic API',
                                            ])
                                            ->default('fonnte')
                                            ->required(fn ($get) => $get('notif_terlambat_aktif')),

                                        FormComponents\TextInput::make('wa_api_token')
                                            ->label('API Token / Key')
                                            ->placeholder('Masukkan token API WhatsApp Gateway Anda')
                                            ->password()
                                            ->revealable()
                                            ->required(fn ($get) => $get('notif_terlambat_aktif')),

                                        FormComponents\TextInput::make('wa_api_endpoint')
                                            ->label('Custom API Endpoint (Opsional)')
                                            ->placeholder('https://api.wablas.com atau webhook URL')
                                            ->url()
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Identitas Lembaga & Kop Surat')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Section::make('Profil Sekolah')
                                    ->schema([
                                        FormComponents\TextInput::make('nama_sekolah')
                                            ->label('Nama Resmi Sekolah / Instansi')
                                            ->required()
                                            ->maxLength(150),
                                        FormComponents\TextInput::make('npsn')
                                            ->label('NPSN')
                                            ->maxLength(30),
                                        FormComponents\Textarea::make('alamat')
                                            ->label('Alamat Lengkap Sekolah')
                                            ->columnSpanFull(),
                                        FormComponents\TextInput::make('telepon')
                                            ->label('Nomor Telepon')
                                            ->tel(),
                                        FormComponents\TextInput::make('email')
                                            ->label('Email Resmi')
                                            ->email(),
                                        FormComponents\TextInput::make('kepala_sekolah')
                                            ->label('Nama Kepala Sekolah (beserta Gelar)'),
                                        FormComponents\TextInput::make('nip_kepala_sekolah')
                                            ->label('NIP Kepala Sekolah'),
                                        FormComponents\FileUpload::make('logo')
                                            ->label('Logo Sekolah')
                                            ->image()
                                            ->disk('public')
                                            ->directory('sekolah-logo')
                                            ->columnSpanFull(),
                                        FormComponents\Textarea::make('teks_pengumuman_display')
                                            ->label('Teks Berjalan / Pengumuman Layar TV Lobi')
                                            ->placeholder('Contoh: Selamat datang di UPTD SPF SD Inpres Rappojawa — Mohon seluruh guru melakukan presensi tepat waktu.')
                                            ->helperText('Teks ini akan ditampilkan sebagai running text berjalan di bagian bawah layar TV lobi sekolah.')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function getPengaturanProperty(): PengaturanSekolah
    {
        return PengaturanSekolah::getSetting();
    }

    public function simpan(): void
    {
        $data = $this->form->getState();

        $setting = PengaturanSekolah::getSetting();
        $setting->update($data);

        $this->form->fill($setting->fresh()->toArray());

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->body('Konfigurasi sekolah, geofencing, dan WhatsApp telah diperbarui.')
            ->success()
            ->send();
    }

    public function setKoordinatDariGps(float $lat, float $lng): void
    {
        $this->data['latitude'] = $lat;
        $this->data['longitude'] = $lng;

        Notification::make()
            ->title('Koordinat GPS berhasil diperoleh')
            ->body("Latitude: {$lat}, Longitude: {$lng}")
            ->success()
            ->send();
    }

    public function notifikasiGpsBerhasil(float $lat, float $lng): void
    {
        $this->setKoordinatDariGps($lat, $lng);
    }

    public function updateKoordinat(float $lat, float $lng): void
    {
        $this->data['latitude'] = $lat;
        $this->data['longitude'] = $lng;
    }

    public function testKirimWa(): void
    {
        $setting = PengaturanSekolah::getSetting();

        if (! $setting->no_wa_kepala_sekolah || ! $setting->wa_api_token) {
            Notification::make()
                ->title('Nomor WA atau Token API belum diisi')
                ->body('Pastikan Anda telah mengisi nomor WhatsApp Kepala Sekolah dan API Token lalu menyimpannya terlebih dahulu.')
                ->warning()
                ->send();

            return;
        }

        $pesanTest = "✅ *UJI COBA KONEKSI SISTEM ABSENSI GURU*\n\n"
            .'Pesan ini adalah uji coba pengiriman notifikasi WhatsApp dari Sistem Absensi Guru ('.($setting->nama_sekolah ?: 'Sekolah').").\n"
            ."Status: Integrasi WhatsApp Gateway Berhasil Terhubung!\n"
            .'Waktu: '.now()->translatedFormat('d F Y H:i:s').' WITA.';

        $result = WhatsAppService::kirimPesan($setting->no_wa_kepala_sekolah, $pesanTest);

        if ($result['success']) {
            Notification::make()
                ->title('Pesan WhatsApp Berhasil Terkirim!')
                ->body('Pesan uji coba telah dikirimkan ke '.$setting->no_wa_kepala_sekolah)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal Mengirim WhatsApp')
                ->body($result['message'] ?? 'Terjadi kesalahan saat menghubungkan ke provider.')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testKirimWa')
                ->label('Uji Coba WA')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->action('testKirimWa'),

            Action::make('simpanHeader')
                ->label('Simpan Pengaturan')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->keyBindings(['mod+s'])
                ->action('simpan'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('simpan')
                ->label('Simpan Pengaturan')
                ->submit('simpan')
                ->color('primary'),
        ];
    }
}
