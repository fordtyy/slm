<?php

namespace App\Models;

use App\Enums\BorrowStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'start_date',
        'due_date',
        'status',
        'user_id'
    ];

    protected $dates = ['due_date', 'start_date'];

    protected $casts = [
        'status' => BorrowStatus::class,
        'start_date' => 'datetime',
        'due_date' => 'datetime'
    ];

    protected $attributes = [
        'status' => BorrowStatus::PENDING
    ];

    /**
     * The "booted" method of the model.
     */
    public static function booted()
    {
        parent::boot();

        self::creating(function (Borrow $model) {
            $date = now()->format('Ymd-');

            $totalBorrowToday = Borrow::whereDate('created_at', now()->today())->count();

            $model->code = 'BR-' . $date . str($totalBorrowToday + 1)->padLeft(3, '0');
        });
    }

    /**
     * Get all of the books for the Borrow
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    /**
     * Get the user that owns the Borrow
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the bookborrow for the Borrow
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookborrows(): HasMany
    {
        return $this->hasMany(BookBorrow::class);
    }

    /**
     * Get the extension associated with the Borrow
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function extension(): HasOne
    {
        return $this->hasOne(Extension::class);
    }

    public function canBeExtended(): bool
    {
        return $this->status == BorrowStatus::RELEASED
            && $this->due_date?->gte(now())
            && !$this->extension()->exists();
    }
}
