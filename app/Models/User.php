<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_active && $this->role === UserRole::Admin;
    }

    public function canVerify(): bool
    {
        return $this->is_active && in_array($this->role, [UserRole::Admin, UserRole::Verifikator], true);
    }

    public function canManageOperations(): bool
    {
        return $this->is_active && in_array($this->role, [UserRole::Admin, UserRole::Operator], true);
    }

    public function canLinkReportToIncident(): bool
    {
        return $this->is_active && in_array($this->role, [UserRole::Admin, UserRole::Operator], true);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function verifiedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'verified_by_user_id');
    }

    public function createdIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'created_by_user_id');
    }

    public function followUpRecords(): HasMany
    {
        return $this->hasMany(FollowUpRecord::class, 'user_id');
    }

    public function verificationHistories(): HasMany
    {
        return $this->hasMany(VerificationHistory::class, 'user_id');
    }

    public function intelligenceComparisons(): HasMany
    {
        return $this->hasMany(IntelligenceComparison::class, 'user_id');
    }
}
