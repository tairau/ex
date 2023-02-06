<?php

declare(strict_types = 1);

namespace App\Services\Auth;

use App\Data\Auth\LoginCredentials;
use App\Data\Auth\RegistrationForm;
use App\Services\User\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    private const NULL_USER_AGENT = 'Unknown';

    public function __construct(
        private readonly UserRepository $repository,
        private readonly Translator $translator,
        private readonly Hasher $hasher,
    ) {
    }

    /**
     * @param \App\Data\Auth\RegistrationForm $registrationForm
     * @param string|null                     $userAgent
     *
     * @return \Laravel\Sanctum\NewAccessToken
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registration(
        RegistrationForm $registrationForm,
        string|null $userAgent,
    ): NewAccessToken {
        $user = $this->repository->findByEmail($registrationForm->email);

        if ($user) {
            throw ValidationException::withMessages([
                'email' => $this->translator->get('auth.exists'),
            ]);
        }

        $user = $this->repository->create($registrationForm);

        return $this->repository->createToken($user, $this->tokenName($userAgent));
    }

    /**
     * @param \App\Data\Auth\LoginCredentials $credentials
     * @param string|null                     $userAgent
     *
     * @return \Laravel\Sanctum\NewAccessToken
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(
        LoginCredentials $credentials,
        string|null $userAgent
    ): NewAccessToken {
        $user = $this->repository->findByEmail($credentials->email);

        if ($user === null) {
            throw ValidationException::withMessages([
                'password' => $this->translator->get('auth.failed'),
            ]);
        }

        if (! $this->hasher->check($credentials->password, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'password' => $this->translator->get('auth.failed'),
            ]);
        }

        return $this->repository->createToken($user, $this->tokenName($userAgent));
    }

    private function tokenName(string|null $userAgent): string
    {
        return $userAgent ?? static::NULL_USER_AGENT;
    }
}
