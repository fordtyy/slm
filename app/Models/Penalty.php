<?php

namespace App\Models;

use App\Enums\PenaltyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'amount',
        'remarks',
        'user_id',
        'borrow_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PenaltyStatus::class,
        ];
    }

    /**
     * Get the user that owns the Penalty
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the borrow that owns the Penalty
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function borrow(): BelongsTo
    {
        return $this->belongsTo(Borrow::class);
    }
}
