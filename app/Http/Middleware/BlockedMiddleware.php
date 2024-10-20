<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockedMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (Filament::auth()->user()->type === UserType::STUDENT && Filament::auth()->user()->blocked_at === null) {
      return $next($request);
    } else {
      return redirect('/blocked-page');
    }

    return redirect('/admin');
  }
}
