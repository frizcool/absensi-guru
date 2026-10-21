<?php

namespace Tests\Feature;

use App\Filament\Pages\LogMonitoringPage;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SystemLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LogMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@sekolah.sch.id',
            'name' => 'Super Administrator',
        ]);
        $this->superAdmin->assignRole($superAdminRole);

        $this->regularUser = User::factory()->create([
            'email' => 'guru@sekolah.sch.id',
            'name' => 'Guru Biasa',
        ]);
        $this->regularUser->assignRole($guruRole);
    }

    public function test_super_admin_dapat_mengakses_halaman_monitoring_log(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/sekolahku/panel/monitoring-log');
        $response->assertSuccessful();
        $response->assertSee('Pusat Monitoring Log', false);
        $response->assertSee('Log Aktivitas Pengguna', false);
        $response->assertSee('Log Sistem', false);
    }

    public function test_user_non_super_admin_tidak_dapat_mengakses_halaman_monitoring_log(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/sekolahku/panel/monitoring-log');
        $response->assertForbidden();
    }

    public function test_pencatatan_activity_log_berfungsi_dan_dapat_difilter(): void
    {
        $this->actingAs($this->superAdmin);

        ActivityLog::record(
            action: 'CREATE',
            module: 'Guru',
            description: 'Menambahkan data guru baru bernama Budi Hartono',
            user: $this->superAdmin,
            properties: ['nip' => '198701012010011001']
        );

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'CREATE',
            'module' => 'Guru',
            'user_name' => 'Super Administrator',
        ]);

        Livewire::test(LogMonitoringPage::class)
            ->assertSee('Menambahkan data guru baru bernama Budi Hartono')
            ->set('activityModule', 'Guru')
            ->assertSee('Menambahkan data guru baru bernama Budi Hartono')
            ->set('activitySearch', 'Budi Hartono')
            ->assertSee('Menambahkan data guru baru bernama Budi Hartono')
            ->set('activitySearch', 'TidakAdaSamaSekali')
            ->assertDontSee('Menambahkan data guru baru bernama Budi Hartono');
    }

    public function test_modal_detail_activity_log_menampilkan_payload(): void
    {
        $this->actingAs($this->superAdmin);

        $log = ActivityLog::record(
            action: 'UPDATE',
            module: 'Pengaturan',
            description: 'Memperbarui koordinat GPS',
            user: $this->superAdmin,
            properties: ['radius' => 150]
        );

        Livewire::test(LogMonitoringPage::class)
            ->call('viewActivityDetail', $log->id)
            ->assertSee('Detail Log Aktivitas #'.$log->id)
            ->assertSee('Memperbarui koordinat GPS')
            ->assertSee('150')
            ->call('closeActivityDetail')
            ->assertSet('selectedActivityId', null);
    }

    public function test_pembersihan_log_aktivitas_lama(): void
    {
        $this->actingAs($this->superAdmin);

        $oldLogId = DB::table('activity_logs')->insertGetId([
            'user_id' => $this->superAdmin->id,
            'user_name' => $this->superAdmin->name,
            'action' => 'TEST',
            'module' => 'Sistem',
            'description' => 'Log lama yang harus dihapus',
            'created_at' => now()->subDays(45),
            'updated_at' => now()->subDays(45),
        ]);

        $recentLog = ActivityLog::record(
            action: 'LOGIN',
            module: 'Auth',
            description: 'Log baru yang harus tetap ada',
            user: $this->superAdmin
        );

        Livewire::test(LogMonitoringPage::class)
            ->call('clearOldActivityLogs', 30);

        $this->assertDatabaseMissing('activity_logs', ['id' => $oldLogId]);
        $this->assertDatabaseHas('activity_logs', ['id' => $recentLog->id]);
    }

    public function test_system_log_service_membaca_dan_mengosongkan_berkas_log(): void
    {
        $testLogFile = storage_path('logs/test_unit.log');
        File::put($testLogFile, "[2026-10-21 10:00:00] local.ERROR: Test error message {\"ctx\":\"test\"}\n#0 Stack trace line 1\n");

        $service = new SystemLogService;
        $logs = $service->getLogs('test_unit.log');

        $this->assertNotEmpty($logs['entries']);
        $this->assertSame('ERROR', $logs['entries'][0]['level']);
        $this->assertStringContainsString('Test error message', $logs['entries'][0]['message']);

        $service->clearLog('test_unit.log');
        $this->assertSame('', File::get($testLogFile));

        File::delete($testLogFile);
    }
}
