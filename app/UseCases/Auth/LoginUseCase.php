<?php

namespace App\UseCases\Auth;

use App\DTOs\Auth\LoginData;
use App\Models\User;
use App\Services\AuthService;

final class LoginUseCase
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function execute(LoginData $data): User
    {
        return $this->auth->authenticate($data);
    }
}
