<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $targetUser;

    protected Role $superAdminRole;

    protected Role $adminRole;

    protected Role $guruRole;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->guruRole = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        // Permissions needed for admin
        foreach (['ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $this->adminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('superpassword'),
        ]);
        $this->superAdmin->assignRole($this->superAdminRole);

        $this->admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@test.com',
            'password' => Hash::make('adminpassword'),
        ]);
        $this->admin->assignRole($this->adminRole);

        $this->targetUser = User::create([
            'name' => 'Guru Pengguna',
            'email' => 'guru@test.com',
            'password' => Hash::make('gurupassword'),
        ]);
        $this->targetUser->assignRole($this->guruRole);
    }

    public function test_super_admin_can_see_super_admin_in_user_table(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$this->superAdmin, $this->admin, $this->targetUser]);
    }

    public function test_regular_admin_cannot_see_super_admin_in_user_table(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$this->admin, $this->targetUser])
            ->assertCanNotSeeTableRecords([$this->superAdmin]);
    }

    public function test_regular_admin_cannot_access_super_admin_edit_page(): void
    {
        $this->actingAs($this->admin);

        $this->get('/sekolahku/panel/users/'.$this->superAdmin->getKey().'/edit')
            ->assertNotFound();
    }

    public function test_super_admin_can_access_super_admin_edit_page(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(EditUser::class, ['record' => $this->superAdmin->getKey()])
            ->assertSuccessful();
    }

    public function test_update_password_on_edit_user_page_works_and_hashes_correctly(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(EditUser::class, ['record' => $this->targetUser->getKey()])
            ->fillForm([
                'password' => 'newsecretpass123',
                'password_confirmation' => 'newsecretpass123',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->targetUser->refresh();
        $this->assertTrue(Hash::check('newsecretpass123', $this->targetUser->password));
    }

    public function test_empty_password_on_edit_user_page_leaves_old_password_unchanged(): void
    {
        $this->actingAs($this->superAdmin);
        $oldHash = $this->targetUser->password;

        Livewire::test(EditUser::class, ['record' => $this->targetUser->getKey()])
            ->fillForm([
                'name' => 'Guru Berganti Nama',
                'password' => null,
                'password_confirmation' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->targetUser->refresh();
        $this->assertSame('Guru Berganti Nama', $this->targetUser->name);
        $this->assertSame($oldHash, $this->targetUser->password);
    }

    public function test_role_can_be_updated_and_persists_in_database(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(EditUser::class, ['record' => $this->targetUser->getKey()])
            ->fillForm([
                'roles' => [$this->adminRole->id],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->targetUser->refresh();
        $this->assertTrue($this->targetUser->hasRole('admin'));
        $this->assertFalse($this->targetUser->hasRole('guru'));
    }

    public function test_regular_admin_does_not_have_super_admin_in_roles_dropdown(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(CreateUser::class);
        $schema = $component->instance()->getSchema('form');
        $rolesField = $schema->getComponent('roles');

        $options = $rolesField->getOptions();
        $this->assertArrayNotHasKey($this->superAdminRole->id, $options);
        $this->assertArrayHasKey($this->adminRole->id, $options);
        $this->assertArrayHasKey($this->guruRole->id, $options);
    }

    public function test_change_password_table_action_updates_password_successfully(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ListUsers::class)
            ->callTableAction('changePassword', $this->targetUser, [
                'new_password' => 'modalnewpass999',
                'new_password_confirmation' => 'modalnewpass999',
            ])
            ->assertHasNoTableActionErrors();

        $this->targetUser->refresh();
        $this->assertTrue(Hash::check('modalnewpass999', $this->targetUser->password));
    }
}
