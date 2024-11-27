<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookBorrow extends Pivot
{
  protected $fillable = [];
  
  /**
   * Get the books that owns the BookBorrow
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function book(): BelongsTo
  {
      return $this->belongsTo(Book::class);
  }

  /**
   * Get the borrows that owns the BookBorrow
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function borrow(): BelongsTo
  {
      return $this->belongsTo(Borrow::class);
  }
}
