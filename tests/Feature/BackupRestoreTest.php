<?php

namespace Tests\Feature;

use App\Filament\Pages\BackupRestorePage;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackupRestoreTest extends TestCase
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

    public function test_super_admin_dapat_mengakses_halaman_backup_restore(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/sekolahku/panel/backup-restore');
        $response->assertSuccessful();
        $response->assertSee('Cadangan &amp; Pemulihan Data', false);
        $response->assertSee('Buat Backup Baru', false);
        $response->assertSee('Upload', false);
    }

    public function test_user_non_super_admin_dilarang_mengakses_backup_restore(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/sekolahku/panel/backup-restore');
        $response->assertForbidden();
    }

    public function test_backup_service_dapat_membuat_dan_mendaftar_cadangan(): void
    {
        $service = new BackupService;
        $backup = $service->createBackup(
            type: 'database',
            notes: 'Test backup database',
            user: $this->superAdmin
        );

        $this->assertFileExists($backup['path']);

        $backups = $service->getBackups();
        $this->assertNotEmpty($backups);

        $found = collect($backups)->firstWhere('filename', $backup['filename']);
        $this->assertNotNull($found);
        $this->assertSame('database', $found['type']);
        $this->assertSame('Test backup database', $found['notes']);

        // Clean up created test backup
        $service->deleteBackup($backup['filename'], $this->superAdmin);
        $this->assertFileDoesNotExist($backup['path']);
    }

    public function test_backup_service_membuat_safety_snapshot_saat_restore(): void
    {
        $service = new BackupService;
        $backup = $service->createBackup(
            type: 'database',
            notes: 'Backup sebelum restore',
            user: $this->superAdmin
        );

        $result = $service->restoreBackup($backup['filename'], $this->superAdmin);
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['safety_snapshot']);

        // Verify safety snapshot was created
        $safetySnapshotPath = storage_path('app/backups/'.$result['safety_snapshot']);
        $this->assertFileExists($safetySnapshotPath);

        // Clean up
        $service->deleteBackup($backup['filename']);
        $service->deleteBackup($result['safety_snapshot']);
    }

    public function test_livewire_backup_restore_alur_buat_dan_konfirmasi_restore(): void
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(BackupRestorePage::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('backupType', 'database')
            ->set('backupNotes', 'Livewire test note')
            ->call('createBackup')
            ->assertSet('showCreateModal', false);

        $service = new BackupService;
        $backups = $service->getBackups();
        $this->assertNotEmpty($backups);
        $latest = $backups[0];

        // Test restore with wrong confirmation text
        $component->call('openRestoreModal', $latest['filename'])
            ->assertSet('showRestoreModal', true)
            ->set('confirmRestoreText', 'SALAH')
            ->call('restoreBackup')
            ->assertSet('showRestoreModal', true); // still open because confirmation failed

        // Test restore with correct confirmation
        $component->set('confirmRestoreText', 'PULIHKAN')
            ->call('restoreBackup')
            ->assertSet('showRestoreModal', false);

        // Clean up
        $allBackups = $service->getBackups();
        foreach ($allBackups as $b) {
            $service->deleteBackup($b['filename']);
        }
    }
}
