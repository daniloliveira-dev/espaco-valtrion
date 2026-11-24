<?php

namespace App\Interfaces;

use App\DTOs\Auth\RegisterData;
use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function create(RegisterData $data): User;
}
