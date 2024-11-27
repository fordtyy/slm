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
        'code',
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
     * The "booted" method of the model.
     */
    public static function booted()
    {
        parent::boot();

        self::creating(function (Penalty $model) {
            $date = now()->format('Ymd-');

            $totalPenaltyToday = Penalty::whereDate('created_at', now()->today())->count();

            $model->code = 'PE-' . $date . str($totalPenaltyToday + 1)->padLeft(3, '0');
        });
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
