<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate;

class RedirectToUnifiedLogin extends Authenticate
{
    protected function redirectTo($request): ?string
    {
        return route('school.login');
    }
}
