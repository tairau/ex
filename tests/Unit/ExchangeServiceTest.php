<?php

namespace Tests\Unit;

use App\Data\Exchange\Bid;
use App\Events\ExchangeCreatedEvent;
use App\Models\Exchange;
use App\Models\Rate;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Database\Transactional;
use App\Services\Exchange\ExchangeRepository;
use App\Services\Exchange\ExchangeService;
use App\Services\Rate\RateRepository;
use App\Services\Wallet\WalletRepository;
use Brick\Math\BigDecimal;
use DateTimeInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ExchangeServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Dispatcher
     */
    private $dispatcher;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|WalletRepository
     */
    private $walletRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\App\Services\Exchange\ExchangeRepository
     */
    private $exchangeRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RateRepository
     */
    private $rateRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Transactional
     */
    private $transactional;
    /**
     * @var \App\Services\Exchange\ExchangeService
     */
    private $exchangeService;
    /**
     * @var \App\Models\User|\PHPUnit\Framework\MockObject\MockObject
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutEvents();

        $this->dispatcher = $this->app->make(Dispatcher::class);
        $this->walletRepositoryMock =
            $this->createMock(WalletRepository::class);
        $this->exchangeRepositoryMock =
            $this->createMock(ExchangeRepository::class);
        $this->rateRepositoryMock = $this->createMock(RateRepository::class);
        $this->transactional = $this->app->make(Transactional::class);
        $this->user = $this->createMock(User::class);

        $this->exchangeService = new ExchangeService(
            $this->dispatcher,
            $this->walletRepositoryMock,
            $this->exchangeRepositoryMock,
            $this->rateRepositoryMock,
            $this->transactional
        );
    }

    public function test_successfully_create_an_exchange()
    {
        $this->configureMockForCreate();

        $this->expectsEvents(
            ExchangeCreatedEvent::class,
        );

        $exchange = $this->exchangeService->bid(
            $this->user,
            $this->createBid(amount: 1000)
        );

        $this->assertInstanceOf(Exchange::class, $exchange);
    }

    public function test_insufficient_funds_when_create_an_exchange()
    {
        $this->configureMockForCreate();

        $this->assertThrows(function () {
            $this->exchangeService->bid(
                $this->user,
                $this->createBid(amount: 3000)
            );
        }, ValidationException::class);
    }

    public function test_exchange_resolving()
    {
        $exchange = new Exchange([
            'amount'        => BigDecimal::of(1000),
            'expected_rate' => BigDecimal::of(1.4),
        ]);

        $wallet = new Wallet([
            'balance' => BigDecimal::of(2000),
        ]);

        $rate = new Rate();
        $rate->rate = BigDecimal::of(1.4);

        $this->configureMockForTryResolve($exchange, $rate, $wallet);

        $this->exchangeService->tryResolve($exchange);
    }

    private function configureMockForCreate()
    {
        $wallet = $this->createMock(Wallet::class);

        $wallet
            ->expects($this->once())
            ->method('__get')
            ->with('balance')
            ->willReturn(BigDecimal::of(2000));

        $wallet->expects($this->once())
            ->method('isSameCurrency')
            ->willReturn(false);

        $this->walletRepositoryMock->expects($this->exactly(2))
            ->method('walletByOwner')
            ->willReturn($wallet);
    }

    private function createBid(int $amount): Bid
    {
        return Bid::validateAndCreate([
            'wallet_id'             => 1,
            'destination_wallet_id' => 2,
            'amount'                => $amount,
            'expected_rate'         => 1.4,
            'expired_at'            => now()->addDay()
                ->format(DateTimeInterface::ATOM),
        ]);
    }

    private function configureMockForTryResolve(Exchange $exchange, Rate $rate, Wallet $wallet)
    {
        $this->exchangeRepositoryMock->expects($this->once())
            ->method('lockedExchange')
            ->willReturn($exchange);

        $this->exchangeRepositoryMock->expects($this->once())
            ->method('lockedDestinationWallet')
            ->willReturn($wallet);

        $this->exchangeRepositoryMock->expects($this->once())
            ->method('lockedWallet')
            ->willReturn($wallet);

        $this->exchangeRepositoryMock->expects($this->once())
            ->method('applyRateAndClose');

        $this->rateRepositoryMock->expects($this->once())
            ->method('actualRate')
            ->willReturn($rate);

        $this->walletRepositoryMock->method('persist');

        $this->exchangeRepositoryMock->expects($this->once())
            ->method('refresh')
            ->willReturn($exchange);
    }
}
