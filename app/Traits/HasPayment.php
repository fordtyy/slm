<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasPayment
{
    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
