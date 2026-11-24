<?php

namespace App\UseCases\Auth;

use App\DTOs\Auth\RegisterData;
use App\Models\User;
use App\Services\AuthService;

final class RegisterUseCase
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function execute(RegisterData $data): User
    {
        return $this->auth->register($data);
    }
}
