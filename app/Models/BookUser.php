<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookUser extends Pivot
{
  protected $fillable = [];

  /**
   * Get the book that owns the BookUser
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function book(): BelongsTo
  {
      return $this->belongsTo(Book::class);
  }

  /**
   * Get the user that owns the BookUser
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class);
  }
  
}
