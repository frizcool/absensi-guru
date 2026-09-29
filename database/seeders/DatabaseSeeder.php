<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengajuanIzin;
use App\Models\PengaturanSekolah;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Generate / Refresh semua permissions dari Filament Shield secara non-interaktif
        Artisan::call('shield:generate', [
            '--all' => true,
            '--ignore-existing-policies' => true,
            '--panel' => 'admin',
            '--no-interaction' => true,
        ]);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Inisialisasi 4 Tingkatan Role (Otoritas)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $kepsekRole = Role::firstOrCreate(['name' => 'kepala_sekolah', 'guard_name' => 'web']);
        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        // Set permissions untuk Super Admin (Semua permissions)
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Set permissions untuk Admin (Pengaturan, Shield, Data Master)
        $adminPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', '%:Role')
                ->orWhere('name', 'like', '%:User')
                ->orWhere('name', 'like', '%:Guru')
                ->orWhere('name', 'like', '%:Shift')
                ->orWhere('name', 'like', '%:HariLibur')
                ->orWhere('name', 'like', '%:JadwalShift')
                ->orWhere('name', 'like', '%:JurnalPembelajaran')
                ->orWhere('name', 'like', '%:Presensi')
                ->orWhere('name', 'like', '%:PengajuanIzin')
                ->orWhere('name', 'View:PengaturanSekolahPage')
                ->orWhere('name', 'View:LaporanPresensiPage')
                ->orWhere('name', 'View:MyProfilePage')
                ->orWhere('name', 'View:AdminExecutiveOverviewWidget')
                ->orWhere('name', 'View:AnomaliPresensiWidget')
                ->orWhere('name', 'View:PresensiTrenChartWidget')
                ->orWhere('name', 'View:PresensiStatistikChartWidget')
                ->orWhere('name', 'View:GuruTerbaruPresensiWidget')
                ->orWhere('name', 'View:LeaderboardDisiplinWidget')
                ->orWhere('name', 'View:PeringatanKeterlambatanWidget');
        })->get();
        $adminRole->syncPermissions($adminPermissions);

        // Set permissions untuk Kepala Sekolah (Monitoring, Laporan, Approval Cuti, Rekap, Jurnal)
        $kepsekPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'View%:Presensi')
                ->orWhere('name', 'Update:Presensi')
                ->orWhere('name', 'like', 'View%:PengajuanIzin')
                ->orWhere('name', 'Update:PengajuanIzin')
                ->orWhere('name', 'like', 'View%:Guru')
                ->orWhere('name', 'like', 'View%:Shift')
                ->orWhere('name', 'like', 'View%:HariLibur')
                ->orWhere('name', 'like', 'View%:JadwalShift')
                ->orWhere('name', 'like', 'View%:JurnalPembelajaran')
                ->orWhere('name', 'View:LaporanPresensiPage')
                ->orWhere('name', 'View:MyProfilePage')
                ->orWhere('name', 'View:AdminExecutiveOverviewWidget')
                ->orWhere('name', 'View:AnomaliPresensiWidget')
                ->orWhere('name', 'View:PresensiTrenChartWidget')
                ->orWhere('name', 'View:PresensiStatistikChartWidget')
                ->orWhere('name', 'View:LeaderboardDisiplinWidget')
                ->orWhere('name', 'View:GuruTerbaruPresensiWidget')
                ->orWhere('name', 'View:PeringatanKeterlambatanWidget');
        })->get();
        $kepsekRole->syncPermissions($kepsekPermissions);

        // 2. Akun Super Admin
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.sch.id'],
            [
                'name' => 'Super Administrator (Master)',
                'password' => Hash::make('password'),
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // 3. Akun Admin Sekolah (Pengaturan, Master & Shield)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->assignRole($adminRole);

        // 4. Akun Kepala Sekolah (Laporan & Monitoring)
        $kepsekUser = User::firstOrCreate(
            ['email' => 'kepsek@sekolah.sch.id'],
            [
                'name' => 'Drs. H. Muhammad Nur, M.Pd. (Kepala Sekolah)',
                'password' => Hash::make('password'),
            ]
        );
        $kepsekUser->assignRole($kepsekRole);

        // 2. Shift Kerja
        $shiftPagi = Shift::firstOrCreate(
            ['nama' => 'Shift Pagi Reguler'],
            [
                'jam_masuk' => '07:15:00',
                'jam_buka_masuk' => '05:30:00',
                'jam_tutup_masuk' => '10:00:00',
                'jam_pulang' => '14:30:00',
                'jam_buka_pulang' => '12:00:00',
                'jam_tutup_pulang' => '18:00:00',
                'toleransi_menit' => 15,
            ]
        );

        $shiftSiang = Shift::firstOrCreate(
            ['nama' => 'Shift Full Day'],
            [
                'jam_masuk' => '08:00:00',
                'jam_buka_masuk' => '06:30:00',
                'jam_tutup_masuk' => '11:00:00',
                'jam_pulang' => '16:00:00',
                'jam_buka_pulang' => '14:00:00',
                'jam_tutup_pulang' => '19:00:00',
                'toleransi_menit' => 15,
            ]
        );

        $shiftPiket = Shift::firstOrCreate(
            ['nama' => 'Shift Piket & Ekskul'],
            [
                'jam_masuk' => '09:30:00',
                'jam_buka_masuk' => '08:00:00',
                'jam_tutup_masuk' => '12:00:00',
                'jam_pulang' => '17:30:00',
                'jam_buka_pulang' => '15:00:00',
                'jam_tutup_pulang' => '20:00:00',
                'toleransi_menit' => 15,
            ]
        );

        // 3. Pengaturan Sekolah & Geofencing
        $pengaturan = PengaturanSekolah::updateOrCreate(
            ['id' => 1],
            [
                'nama_sekolah' => 'UPTD SPF SD Inpres Rappojawa',
                'npsn' => '40307123',
                'alamat' => 'Jl. Rappojawa No. 12, Kota Makassar, Sulawesi Selatan',
                'telepon' => '(0411) 456789',
                'email' => 'info@sdinpresrappojawa.sch.id',
                'kepala_sekolah' => 'Drs. H. Muhammad Nur, M.Pd.',
                'nip_kepala_sekolah' => '197001011995031002',
                'latitude' => -5.147665,
                'longitude' => 119.432731,
                'radius_meter' => 120,
                'wajib_validasi_lokasi' => true,
                'notif_terlambat_aktif' => true,
                'no_wa_kepala_sekolah' => '081234567890',
                'ambang_keterlambatan' => 3,
                'wa_provider' => 'fonnte',
                'wa_api_token' => 'DEMO_FONNTE_TOKEN_SEKOLAH_2026',
            ]
        );

        // 4. Daftar Hari Libur
        $daftarLibur = [
            ['nama' => 'Tahun Baru Masehi 2026', 'mulai' => '2026-01-01', 'selesai' => '2026-01-01', 'ket' => 'Tahun Baru 2026'],
            ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW', 'mulai' => '2026-01-16', 'selesai' => '2026-01-16', 'ket' => 'Libur Nasional Keagamaan'],
            ['nama' => 'Tahun Baru Imlek 2577', 'mulai' => '2026-02-17', 'selesai' => '2026-02-17', 'ket' => 'Tahun Baru Imlek'],
            ['nama' => 'Hari Raya Nyepi', 'mulai' => '2026-03-21', 'selesai' => '2026-03-21', 'ket' => 'Tahun Baru Saka 1948'],
            ['nama' => 'Hari Raya Idul Fitri 1447 H', 'mulai' => '2026-03-20', 'selesai' => '2026-03-24', 'ket' => 'Idul Fitri dan Cuti Bersama'],
            ['nama' => 'Hari Kemerdekaan RI', 'mulai' => '2026-08-17', 'selesai' => '2026-08-17', 'ket' => 'HUT Kemerdekaan RI ke-81'],
        ];

        foreach ($daftarLibur as $lbr) {
            HariLibur::firstOrCreate(
                ['nama' => $lbr['nama']],
                [
                    'tanggal_mulai' => $lbr['mulai'],
                    'tanggal_selesai' => $lbr['selesai'],
                    'is_libur_nasional' => true,
                    'keterangan' => $lbr['ket'],
                ]
            );
        }

        // 5. Data 10 Guru Lengkap dengan Akun Login
        $guruDataList = [
            [
                'email' => 'guru@sekolah.sch.id',
                'nama' => 'Ahmad Dahlan, S.Pd.',
                'nip' => '198507122010011015',
                'nuptk' => '4538763665200012',
                'jk' => 'L',
                'status' => 'pns',
                'pangkat' => 'Penata Muda Tk. I / III.b',
                'jabatan' => 'Guru Muda',
                'jenis_guru' => 'Guru Kelas V',
                'no_hp' => '081234567890',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru2@sekolah.sch.id',
                'nama' => 'Siti Rahmawati, S.Pd.I.',
                'nip' => '199003152019032008',
                'nuptk' => '8435768669210023',
                'jk' => 'P',
                'status' => 'pppk',
                'pangkat' => 'Ahli Pertama / IX',
                'jabatan' => 'Guru Pertama',
                'jenis_guru' => 'Guru PAI Kelas I-VI',
                'no_hp' => '082198765432',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru3@sekolah.sch.id',
                'nama' => 'Budi Santoso, S.Pd.',
                'nip' => '199211052020121004',
                'nuptk' => '1234567890123456',
                'jk' => 'L',
                'status' => 'non_pns',
                'pangkat' => '-',
                'jabatan' => 'Guru Honorer',
                'jenis_guru' => 'Guru PJOK',
                'no_hp' => '085211223344',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru4@sekolah.sch.id',
                'nama' => 'Dewi Lestari, S.Pd., M.Pd.',
                'nip' => '198204182008012011',
                'nuptk' => '6543219876543210',
                'jk' => 'P',
                'status' => 'pns',
                'pangkat' => 'Penata Tk. I / III.d',
                'jabatan' => 'Guru Madya / Wakasek Kurikulum',
                'jenis_guru' => 'Guru Kelas I',
                'no_hp' => '081344556677',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru5@sekolah.sch.id',
                'nama' => 'Hendra Wijaya, S.Pd.',
                'nip' => '198809202015021003',
                'nuptk' => '7890123456789012',
                'jk' => 'L',
                'status' => 'pns',
                'pangkat' => 'Penata / III.c',
                'jabatan' => 'Guru Muda',
                'jenis_guru' => 'Guru Kelas VI',
                'no_hp' => '081399887766',
                'shift_id' => $shiftSiang->id,
            ],
            [
                'email' => 'guru6@sekolah.sch.id',
                'nama' => 'Nurul Hidayah, S.Pd.',
                'nip' => '199405102022032014',
                'nuptk' => '3456789012345678',
                'jk' => 'P',
                'status' => 'pppk',
                'pangkat' => 'Ahli Pertama / IX',
                'jabatan' => 'Guru Pertama',
                'jenis_guru' => 'Guru Bahasa Inggris',
                'no_hp' => '082211445566',
                'shift_id' => $shiftSiang->id,
            ],
            [
                'email' => 'guru7@sekolah.sch.id',
                'nama' => 'Rizky Pratama, S.Pd.',
                'nip' => null,
                'nuptk' => '9012345678901234',
                'jk' => 'L',
                'status' => 'non_pns',
                'pangkat' => '-',
                'jabatan' => 'Guru Honorer',
                'jenis_guru' => 'Guru Kelas II',
                'no_hp' => '085388776655',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru8@sekolah.sch.id',
                'nama' => 'Sri Wahyuni, S.Pd.',
                'nip' => '198612252011012018',
                'nuptk' => '5678901234567890',
                'jk' => 'P',
                'status' => 'pns',
                'pangkat' => 'Penata Muda Tk. I / III.b',
                'jabatan' => 'Guru Muda',
                'jenis_guru' => 'Guru Kelas III',
                'no_hp' => '081277665544',
                'shift_id' => $shiftPagi->id,
            ],
            [
                'email' => 'guru9@sekolah.sch.id',
                'nama' => 'Andi Tenri, S.Kom.',
                'nip' => '199508142023022009',
                'nuptk' => '2345678901234567',
                'jk' => 'P',
                'status' => 'pppk',
                'pangkat' => 'Ahli Pertama / IX',
                'jabatan' => 'Tenaga Administrasi Sekolah',
                'jenis_guru' => 'Operator & Tata Usaha',
                'no_hp' => '082355443322',
                'shift_id' => $shiftPiket->id,
            ],
            [
                'email' => 'guru10@sekolah.sch.id',
                'nama' => 'Fatimah Zahra, S.Pd.',
                'nip' => '198901022014032005',
                'nuptk' => '4321098765432109',
                'jk' => 'P',
                'status' => 'pns',
                'pangkat' => 'Penata / III.c',
                'jabatan' => 'Guru Muda',
                'jenis_guru' => 'Guru Kelas IV',
                'no_hp' => '081322334455',
                'shift_id' => $shiftPagi->id,
            ],
        ];

        $createdGurus = [];

        foreach ($guruDataList as $gData) {
            $defaultPassword = $gData['email'] === 'guru@sekolah.sch.id'
                ? 'password'
                : (! empty($gData['nip']) ? $gData['nip'] : 'password');

            $user = User::updateOrCreate(
                ['email' => $gData['email']],
                [
                    'name' => $gData['nama'],
                    'nip' => $gData['nip'],
                    'password' => Hash::make($defaultPassword),
                ]
            );
            $user->assignRole($guruRole);

            $guru = Guru::updateOrCreate(
                ['nama' => $gData['nama']],
                [
                    'user_id' => $user->id,
                    'nip' => $gData['nip'],
                    'nuptk' => $gData['nuptk'],
                    'jenis_kelamin' => $gData['jk'],
                    'status_kepegawaian' => $gData['status'],
                    'pangkat_golongan' => $gData['pangkat'],
                    'jabatan' => $gData['jabatan'],
                    'jenis_guru' => $gData['jenis_guru'],
                    'no_hp' => $gData['no_hp'],
                    'jumlah_jam' => 24,
                    'shift_id' => $gData['shift_id'],
                    'aktif' => true,
                ]
            );

            $createdGurus[] = $guru;
        }

        // 6. Generate Riwayat Presensi Realistis Selama 30 Hari Terakhir Hingga Kemarin
        $startDate = Carbon::today()->subDays(29);
        $endDate = Carbon::yesterday();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $isWeekend = $date->isWeekend();
            $dateString = $date->format('Y-m-d');

            // Lewati akhir pekan untuk presensi reguler
            if ($isWeekend) {
                continue;
            }

            foreach ($createdGurus as $idx => $guru) {
                $shift = $guru->shift ?? $shiftPagi;

                // Variasi status kehadiran realistis:
                // 80% Tepat waktu, 10% Terlambat, 5% Izin/Sakit, 5% Cuti/Alpa
                $rand = rand(1, 100);

                $record = Presensi::where('guru_id', $guru->id)->whereDate('tanggal', $dateString)->first();
                if (! $record) {
                    $record = new Presensi([
                        'guru_id' => $guru->id,
                        'tanggal' => $dateString,
                    ]);
                }

                if ($rand <= 80) {
                    // HADIR TEPAT WAKTU
                    $menitMasuk = rand(0, 14); // 07:01 - 07:14
                    $jamMasuk = $date->copy()->setTime(7, $menitMasuk, rand(10, 50));
                    $jamPulang = $date->copy()->setTime(14, rand(30, 45), rand(10, 50));

                    $record->fill([
                        'shift_id' => $shift->id,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status_kehadiran' => 'hadir',
                        'status_masuk' => 'tepat_waktu',
                        'status_pulang' => 'normal',
                        'lokasi_masuk_lat' => -5.147665 + (rand(-10, 10) / 100000),
                        'lokasi_masuk_lng' => 119.432731 + (rand(-10, 10) / 100000),
                        'lokasi_pulang_lat' => -5.147665 + (rand(-10, 10) / 100000),
                        'lokasi_pulang_lng' => 119.432731 + (rand(-10, 10) / 100000),
                    ])->save();
                } elseif ($rand <= 92) {
                    // TERLAMBAT
                    $menitTerlambat = rand(20, 50); // 07:20 - 07:50
                    $jamMasuk = $date->copy()->setTime(7, $menitTerlambat, rand(10, 50));
                    $jamPulang = $date->copy()->setTime(14, rand(30, 40), rand(10, 50));

                    $record->fill([
                        'shift_id' => $shift->id,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status_kehadiran' => 'hadir',
                        'status_masuk' => 'terlambat',
                        'status_pulang' => 'normal',
                        'keterangan' => 'Terlambat '.($menitTerlambat - 15).' menit karena macet jalan raya',
                        'lokasi_masuk_lat' => -5.147665,
                        'lokasi_masuk_lng' => 119.432731,
                    ])->save();
                } elseif ($rand <= 96) {
                    // SAKIT
                    $record->fill([
                        'shift_id' => $shift->id,
                        'status_kehadiran' => 'sakit',
                        'keterangan' => 'Sakit demam / istirahat dokter',
                    ])->save();
                } elseif ($rand <= 98) {
                    // IZIN
                    $record->fill([
                        'shift_id' => $shift->id,
                        'status_kehadiran' => 'izin',
                        'keterangan' => 'Izin menghadiri seminar / dinas luar',
                    ])->save();
                }
            }
        }

        // 7. Data Pengajuan Izin & Cuti Guru Realistis
        PengajuanIzin::firstOrCreate(
            ['alasan' => 'Demam tinggi dan flu berat, istirahat atas saran dokter spesialis.'],
            [
                'guru_id' => $createdGurus[0]->id,
                'jenis' => 'sakit',
                'tanggal_mulai' => Carbon::today()->subDays(10)->toDateString(),
                'tanggal_selesai' => Carbon::today()->subDays(9)->toDateString(),
                'status' => 'disetujui',
                'catatan_approval' => 'Disetujui. Semoga lekas sembuh dan dapat beraktivitas kembali.',
                'disetujui_oleh' => $adminUser->id,
            ]
        );

        PengajuanIzin::firstOrCreate(
            ['alasan' => 'Menghadiri acara wisuda anak di Universitas Hasanuddin Makassar.'],
            [
                'guru_id' => $createdGurus[1]->id,
                'jenis' => 'izin',
                'tanggal_mulai' => Carbon::today()->subDays(4)->toDateString(),
                'tanggal_selesai' => Carbon::today()->subDays(4)->toDateString(),
                'status' => 'disetujui',
                'catatan_approval' => 'Disetujui. Selamat atas kelulusan putranya.',
                'disetujui_oleh' => $adminUser->id,
            ]
        );

        PengajuanIzin::firstOrCreate(
            ['alasan' => 'Pengajuan Cuti Tahunan untuk urusan keluarga di luar kota.'],
            [
                'guru_id' => $createdGurus[3]->id,
                'jenis' => 'cuti',
                'tanggal_mulai' => Carbon::today()->addDays(2)->toDateString(),
                'tanggal_selesai' => Carbon::today()->addDays(4)->toDateString(),
                'status' => 'menunggu',
                'catatan_approval' => null,
            ]
        );
    }
}
