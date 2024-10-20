<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class AccountMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if (Filament::auth() && Filament::auth()->user()->type === UserType::STUDENT) {
      return $next($request);
    }

    return redirect('/admin');
  }
}
