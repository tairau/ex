<?php

declare(strict_types = 1);

namespace App\Services\User;

use App\Data\Auth\RegistrationForm;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Sanctum\NewAccessToken;

class UserRepository
{
    public function __construct(
        private readonly Hasher $hasher,
    ) {
    }

    /**
     * @param \App\Data\Auth\RegistrationForm $registrationForm
     *
     * @return \App\Models\User
     */
    public function create(RegistrationForm $registrationForm): User
    {
        $user = new User([
            'name'     => $registrationForm->name,
            'email'    => $registrationForm->email,
            'password' => $this->hasher->make($registrationForm->password),
        ]);
        $this->persist($user);

        return $user;
    }

    /**
     * @param \App\Models\User $user
     * @param string           $tokenName
     *
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(User $user, string $tokenName): NewAccessToken
    {
        return $user->createToken($tokenName);
    }

    /**
     * @param \App\Models\User $user
     *
     * @return void
     */
    private function persist(User $user): void
    {
        $user->save();
    }

    /**
     * @param string $email
     *
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email): User|null
    {
        /** @var \App\Models\User|null */
        return User::query()
            ->where('email', $email)
            ->first();
    }
}
