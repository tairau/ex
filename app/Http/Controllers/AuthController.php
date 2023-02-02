<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Data\Auth\LoginCredentials;
use App\Data\Auth\RegistrationForm;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $response,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @param \App\Data\Auth\RegistrationForm $registrationForm
     * @param \Illuminate\Http\Request        $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registration(
        RegistrationForm $registrationForm,
        Request $request,
    ): Response
    {
        $token = $this->authService->registration(
            $registrationForm,
            $request->userAgent()
        );

        return $this->response->json([
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * @param \App\Data\Auth\LoginCredentials $credentials
     * @param \Illuminate\Http\Request        $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginCredentials $credentials, Request $request): Response
    {
        $token = $this->authService->login($credentials, $request->userAgent());

        return $this->response->json([
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function me(): Response
    {
        $user = $this->authManager->user();

        return $this->response->json([
            'data' => UserResource::make($user)
        ]);
    }
}
