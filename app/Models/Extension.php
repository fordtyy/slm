<?php

namespace App\Models;

use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_of_days',
        'status',
        'reason',
        'borrow_id'
    ];

    protected $casts = [
        'status' => ExtensionStatus::class,
        'number_of_days' => ExtensionDays::class
    ];

    protected $attributes = [
        'status' => ExtensionStatus::PENDING
    ];

    /**
     * The "booted" method of the model.
     */
    public static function booted()
    {
        parent::boot();

        self::creating(function (Extension $model) {
            $date = now()->format('Ymd-');

            $totalBorrowToday = Extension::whereDate('created_at', now()->today())->count();

            $model->code = 'ER-' . $date . str($totalBorrowToday + 1)->padLeft(3, '0');
        });
    }

    /**
     * Get the borrow that owns the Extension
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function borrow(): BelongsTo
    {
        return $this->belongsTo(Borrow::class);
    }
}
