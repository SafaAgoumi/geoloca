<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check'; 

    protected static string $view = 'filament.pages.admin-dashboard';

       public static function shouldRegisterNavigation(): bool
{
    return optional(Auth::user())->isAdmin() ?? false;
}

}