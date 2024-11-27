<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    /**
     * The books that belong to the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get all of the categoryPrefs for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categoryPrefs(): HasMany
    {
        return $this->hasMany(CategoryUser::class);
    }
}
