<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterData;
use App\DTOs\Auth\UserData;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\UseCases\Auth\LoginUseCase;
use App\UseCases\Auth\LogoutUseCase;
use App\UseCases\Auth\RegisterUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginUseCase $login): JsonResponse
    {
        $data = new LoginData(
            email: $request->validated('email'),
            password: $request->validated('password'),
            remember: $request->boolean('remember'),
        );
        $user = $login->execute($data);

        Auth::guard('web')->login($user, $data->remember);
        $request->session()->regenerate();

        return response()->json(['user' => UserData::fromUser($user)->toArray()]);
    }

    public function register(RegisterRequest $request, RegisterUseCase $register): JsonResponse
    {
        $user = $register->execute(new RegisterData(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        ));

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return response()->json(['user' => UserData::fromUser($user)->toArray()], 201);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        return response()->json(['user' => UserData::fromUser($user)->toArray()]);
    }

    public function logout(Request $request, LogoutUseCase $logout): JsonResponse
    {
        $logout->execute();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sessão encerrada.']);
    }
}
