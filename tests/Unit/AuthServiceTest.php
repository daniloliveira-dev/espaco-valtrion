<?php

namespace Tests\Unit;

use App\DTOs\Auth\LoginData;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    public function test_it_authenticates_a_user_with_a_valid_password(): void
    {
        $user = new User;
        $user->setRawAttributes([
            'id' => 7,
            'name' => 'Ana Souza',
            'email' => 'ana@hospital.com.br',
            'password' => Hash::make('senha-segura'),
        ]);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->with('ana@hospital.com.br')
            ->andReturn($user);

        $service = new AuthService($repository);
        $authenticated = $service->authenticate(new LoginData(
            email: 'ana@hospital.com.br',
            password: 'senha-segura',
            remember: false,
        ));

        $this->assertSame($user, $authenticated);
    }

    public function test_it_rejects_invalid_login_credentials_with_a_generic_error(): void
    {
        $user = new User;
        $user->setRawAttributes([
            'email' => 'ana@hospital.com.br',
            'password' => Hash::make('senha-correta'),
        ]);

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->andReturn($user);

        $service = new AuthService($repository);

        try {
            $service->authenticate(new LoginData(
                email: 'ana@hospital.com.br',
                password: 'senha-incorreta',
                remember: false,
            ));
            $this->fail('Invalid credentials should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('email', $exception->errors());
            $this->assertSame(
                'As credenciais informadas não conferem.',
                $exception->errors()['email'][0],
            );
        }
    }

    public function test_it_rejects_an_unknown_email_with_the_same_generic_error(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findByEmail')
            ->once()
            ->with('unknown@hospital.com.br')
            ->andReturnNull();

        $service = new AuthService($repository);

        try {
            $service->authenticate(new LoginData(
                email: 'unknown@hospital.com.br',
                password: 'senha-segura',
                remember: false,
            ));
            $this->fail('Unknown email addresses should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                'As credenciais informadas não conferem.',
                $exception->errors()['email'][0],
            );
        }
    }
}
