<?php

namespace App\DTOs\Auth;

use App\Models\User;

final readonly class UserData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self($user->id, $user->name, $user->email);
    }

    /**
     * @return array{id: int, name: string, email: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
