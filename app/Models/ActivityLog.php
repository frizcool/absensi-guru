<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to easily record activity logs throughout the application.
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $user = null
    ): self {
        $currentUser = $user ?? Auth::user();

        $roleName = null;
        if ($currentUser) {
            $roleName = $currentUser->roles?->pluck('name')->first() ?? ($currentUser->hasRole('super_admin') ? 'super_admin' : 'user');
        }

        $ipAddress = null;
        $userAgent = null;

        if (function_exists('request') && request()) {
            $ipAddress = request()->ip();
            $userAgent = request()->userAgent();
        }

        return static::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'Sistem / Anonim',
            'user_role' => $roleName,
            'action' => strtoupper($action),
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
