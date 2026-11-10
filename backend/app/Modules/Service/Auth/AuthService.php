<?php

namespace App\Modules\Service\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): ?array
    {
        $guard = Auth::guard('api');

        $token = $guard->attempt($credentials);

        if (!$token) {
            return null;
        }

        return $this->tokenResponse($guard, $token);
    }

    public function refresh(): array
    {
        $guard = Auth::guard('api');

        // Refresh token JWT yang dikirim melalui Bearer header.
        // Token lama akan diinvalidasi.
        $token = $guard->refresh();

        return $this->tokenResponse($guard, $token);
    }

    public function me(): ?User
    {
        return Auth::guard('api')->user();
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }

    private function tokenResponse($guard, string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ];
    }
}
