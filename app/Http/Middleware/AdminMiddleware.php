<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        if (Filament::auth() && Filament::auth()->user()->type === UserType::ADMIN)
        {
            return $next($request);
        }

        return redirect('/account');
    }
}

?>
