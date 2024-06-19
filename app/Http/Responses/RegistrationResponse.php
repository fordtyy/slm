<?php

namespace App\Http\Responses;

use App\Filament\Account\Resources\BorrowResource as AccountBorrowResource;
use App\Filament\Admin\Resources\BorrowResource;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
 
class RegistrationResponse extends \Filament\Http\Responses\Auth\RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // You can use the Filament facade to get the current panel and check the ID
        if (Filament::auth()->user()->type === 'admin') {
            // dd(Filament::auth()->user()->type);
            return redirect()->to(BorrowResource::getUrl('index'));
        }
 
        if (Filament::auth()->user()->type === 'student') {
            return redirect()->to(AccountBorrowResource::getUrl('index'));
        }
 
        return parent::toResponse($request);
    }
}

?>