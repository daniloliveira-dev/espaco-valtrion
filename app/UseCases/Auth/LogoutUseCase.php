<?php

namespace App\UseCases\Auth;

use Illuminate\Support\Facades\Auth;

final class LogoutUseCase
{
    public function execute(): void
    {
        Auth::guard('web')->logout();
    }
}
