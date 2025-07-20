<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

       if ($user->isAdmin()) {
    return redirect()->route('filament.admin.pages.admin-dashboard');
}

if ($user->isGestionnaire()) {
    return redirect()->route('filament.admin.pages.dashboard');
}

return redirect()->route('filament.admin.pages.visiteur-dashboard');

    }
}