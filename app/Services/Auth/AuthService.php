<?php

declare(strict_types = 1);

namespace App\Services\Auth;

use App\Data\Auth\LoginCredentials;
use App\Data\Auth\RegistrationForm;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

readonly class AuthService
{
    public function __construct(
        private Translator $translator,
        private Hasher $hasher,
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
        $email = Str::lower($registrationForm->email);

        /** @var User $user */
        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user) {
            throw ValidationException::withMessages([
                'email' => $this->translator->get('auth.exists'),
            ]);
        }

        $user = new User([
            'name'     => $registrationForm->name,
            'email'    => $email,
            'password' => $this->hasher->make($registrationForm->password),
        ]);
        $user->save();

        return $user->createToken($userAgent);
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
        /** @var User $user */
        $user = User::query()
            ->where('email', $credentials->email)
            ->first();

        if ($user === null) {
            throw ValidationException::withMessages([
                'password' => $this->translator->get('auth.failed'),
            ]);
        }

        if (! $this->hasher->check($credentials->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => $this->translator->get('auth.failed'),
            ]);
        }

        return $user->createToken($userAgent);
    }
}
