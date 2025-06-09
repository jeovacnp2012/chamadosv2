<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use App\Traits\ChecksResourcePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements BelongsToCompanyInterface
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, ChecksResourcePermission, HasRoles,HasApiTokens;

    protected $with = ['departaments', 'sectors'];
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'supplier_id',
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
        ];
    }
    public function sectors(): BelongsToMany
    {
        return $this->belongsToMany(Sector::class);
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function departaments(): BelongsToMany
    {
        return $this->belongsToMany(Departament::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hasRoleApi($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

}
