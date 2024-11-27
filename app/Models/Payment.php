<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'reference',
        'source_code',
        'status',
        'amount',
        'method',
        'supporting_document',
        'paid_at',
        'remarks'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime'
        ];
    }

    /**
     * The "booted" method of the model.
     */
    public static function booted()
    {
        parent::boot();

        self::creating(function (Payment $model) {
            $date = now()->format('Ymd-');

            $totalPaymentToday = Payment::whereDate('created_at', now()->today())->count();

            $model->code = 'PY-' . $date . str($totalPaymentToday + 1)->padLeft(3, '0');
        });
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
