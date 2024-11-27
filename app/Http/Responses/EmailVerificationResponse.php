<?php

namespace App\Http\Responses;

use App\Filament\Account\Resources\BorrowResource as AccountBorrowResource;
use App\Filament\Admin\Resources\BorrowResource;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class EmailVerificationResponse extends \Filament\Http\Responses\Auth\EmailVerificationResponse
{
  public function toResponse($request): RedirectResponse|Redirector
  {
    return redirect()->route('filament.account.pages.category-preferred');
  }
}
