<?php

declare(strict_types = 1);

namespace Tests\Unit;

use App\Services\ApiLayer\Exchange\ExchangeClient;
use App\Services\ApiLayer\Exchange\Rate;
use App\Services\ApiLayer\Exchange\RateList;
use App\Services\ApiLayer\Exchange\ResponseTransformer;
use App\Services\ApiLayer\Exchange\Symbol;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ExchangeClientTest extends TestCase
{
    /** @var \Illuminate\Http\Client\Response */
    private $response;

    /** @var \App\Services\ApiLayer\Exchange\ExchangeClient */
    private $exchangeClient;

    /** @var PendingRequest|\PHPUnit\Framework\MockObject\MockObject */
    private $clientMock;

    /** @var ResponseTransformer */
    private $transformer;

    public function setUp(): void
    {
        parent::setUp();

        $this->response = $this->createMock(Response::class);
        $this->clientMock = $this->createMock(PendingRequest::class);
        $this->transformer = new ResponseTransformer();

        $this->exchangeClient =
            new ExchangeClient($this->clientMock, $this->transformer);
    }

    public function test_symbols()
    {
        $this->response
            ->expects($this->once())
            ->method('json')
            ->willReturn([
                'BYN' => 'New Belarusian Ruble',
                'RUB' => 'Russian Ruble',
            ]);

        $this->clientMock
            ->expects($this->once())
            ->method('throw')
            ->willReturnSelf();

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->response);

        $collection = $this->exchangeClient->symbols();
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(Symbol::class, $collection->first());
    }

    public function test_latest()
    {
        $baseSymbol = 'USD';

        $this->response
            ->expects($this->once())
            ->method('json')
            ->willReturn([
                'base'  => $baseSymbol,
                'date'  => now()->format('Y-m-d'),
                'rates' => [
                    'AED' => 4.001024,
                    'AFN' => 98.035078,
                    'ALL' => 115.844832,
                    'AMD' => 431.397321,
                    'ANG' => 1.962958,
                    'AOA' => 549.294992,
                    'ARS' => 204.330466,
                    'AUD' => 1.541793,
                    'AWG' => 1.960698,
                ],
            ]);

        $this->clientMock
            ->expects($this->once())
            ->method('throw')
            ->willReturnSelf();

        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->response);

        $list = $this->exchangeClient->latest($baseSymbol);

        $this->assertInstanceOf(RateList::class, $list);
        $this->assertInstanceOf(Rate::class, $list->rates->first());
    }
}
