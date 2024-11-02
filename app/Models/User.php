<?php

namespace App\Models;

use App\Enums\UserType;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{

    use HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'course_id',
        'year_level_id',
        'usn',
        'email_verified_at',
        'blocked_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'type' => UserType::class,
            'blocked_at' => 'datetime'
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->type = 'student';
        });
    }

    public function yearLevelAndCourse(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->yearLevel->name . ' ' . $this->course->code
        );
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function yearLevel(): BelongsTo
    {
        return $this->belongsTo(YearLevel::class);
    }

    /**
     * The books that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wishLists(): HasMany
    {
        return $this->hasMany(BookUser::class);
    }

    /**
     * Get all of the borrows for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Get all of the authorPrefs for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authorPrefs(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * Get all of the categoryPrefs for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categoryPrefs(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get all of the penalties for the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }
}
