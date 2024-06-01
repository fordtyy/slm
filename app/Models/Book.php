<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cover',
        'title',
        'status',
        'description'
    ];

    protected $casts = [
        'status' => Status::class
    ];

    protected $attributes = [
        'status' => Status::DRAFT
    ];


    /**
     * The categories that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }


    /**
     * The borrow that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function borrow(): BelongsToMany
    {
        return $this->belongsToMany(Borrow::class);
    }
}
