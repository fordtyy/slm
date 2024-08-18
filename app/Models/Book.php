<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\DocBlock\TagFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'isbn',
        'cover',
        'title',
        'label',
        'edition',
        'year',
        'category_id',
        'volume',
        'copies'
    ];

    public function authorsName(): Attribute
    {
        return Attribute::make(
            get: fn() => implode(', ', $this->authors->map(fn($author) => $author->name)->all())
        );
    }

    /**
     * The categories that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The authors that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * Get all of the bookborrow for the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookborrows(): HasMany
    {
        return $this->hasMany(BookBorrow::class);
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

    /**
     * The tag that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * The wishlists that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wishlists(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * The user that belong to the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
