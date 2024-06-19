<?php

namespace App\Http\Responses;

use App\Filament\Account\Resources\BorrowResource as AccountBorrowResource;
use App\Filament\Admin\Resources\BorrowResource;
use App\Filament\Customer\Resources\MyOrdersResource;
use App\Filament\Resources\OrderResource;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
 
class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // You can use the Filament facade to get the current panel and check the ID
        if (Filament::auth()->user()->type === 'admin') {
            return redirect('/admin');
        }
 
        if (Filament::auth()->user()->type === 'student') {
            return redirect('/account');
        }
 
        return parent::toResponse($request);
    }
}

?>