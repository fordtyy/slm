<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateLog extends Model
{
    use HasFactory;

    protected $fillable = ['date'];

    public $timestamps = false;

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }
}
