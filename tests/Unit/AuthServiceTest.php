<?php

declare(strict_types = 1);

namespace Tests\Unit;

use App\Data\Auth\LoginCredentials;
use App\Data\Auth\RegistrationForm;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\User\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use WithFaker;

    /** @var \App\Models\User|\PHPUnit\Framework\MockObject\MockObject */
    private $userMock;

    /** @var \Illuminate\Contracts\Hashing\Hasher|\PHPUnit\Framework\MockObject\MockObject */
    private $hasherMock;

    /** @var \Illuminate\Contracts\Translation\Translator|\PHPUnit\Framework\MockObject\MockObject */
    private $translatorMock;

    /**
     * @var \App\Services\User\UserRepository|(\App\Services\User\UserRepository&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \Laravel\Sanctum\NewAccessToken|\PHPUnit\Framework\MockObject\MockObject
     */
    private $accessTokenMock;

    private Hasher $hasher;

    public function setUp(): void
    {
        parent::setUp();

        $this->hasher = $this->app->make(Hasher::class);
        $this->userMock = $this->createMock(User::class);
        $this->accessTokenMock = $this->createMock(NewAccessToken::class);
        $this->repositoryMock = $this->createMock(UserRepository::class);
        $this->translatorMock = $this->createMock(Translator::class);
    }

    public function test_registration()
    {
        $this->repositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->repositoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->userMock);

        $this->repositoryMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn($this->accessTokenMock);

        $service = new AuthService(
            $this->repositoryMock,
            $this->translatorMock,
            $this->hasher
        );

        $service->registration(
            RegistrationForm::validateAndCreate([
                'name'     => $this->faker->name(),
                'email'    => $this->faker->email(),
                'password' => $this->faker->password(12),
            ]),
            $this->faker->userAgent(),
        );
    }

    public function test_is_exception_thrown_on_incorrect_input_data()
    {
        $this->assertThrows(function () {
            RegistrationForm::validateAndCreate([
                'name'     => '',
                'email'    => $this->faker->name(),
                'password' => $this->faker->password(4, 11),
            ]);
        }, ValidationException::class);
    }

    public function test_login()
    {
        $credentials = LoginCredentials::validateAndCreate([
            'email'    => $this->faker->email(),
            'password' => $this->faker->password(12),
        ]);

        $user = $this->createMock(User::class);

        $user->expects($this->once())
            ->method('getAuthPassword')
            ->willReturn($this->hasher->make($credentials->password));

        $this->repositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $this->repositoryMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn($this->accessTokenMock);

        $service = new AuthService(
            $this->repositoryMock,
            $this->translatorMock,
            $this->hasher
        );

        $service->login($credentials, $this->faker->userAgent());
    }
}
