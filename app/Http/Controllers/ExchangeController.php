<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Data\Exchange\Bid;
use App\Http\Resources\ExchangeResource;
use App\Http\Resources\LengthAwarePaginatorMetaResource;
use App\Services\Exchange\ExchangeService;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class ExchangeController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly AuthManager $authManager,
        private readonly ExchangeService $exchangeService,
    ) {
    }

    /**
     * @param \App\Data\Exchange\Bid $bid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function bid(Bid $bid): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $exchange = $this->exchangeService->bid($user, $bid);

        return $this->response->json([
            'data' => ExchangeResource::make($exchange),
        ]);
    }

    public function cancel(int $exchangeId): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $this->exchangeService->cancel($user, $exchangeId);

        return $this->response->noContent();
    }

    public function all(): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $paginator = $this->exchangeService->paginateForUser($user);

        return $this->response->json([
            'data' => ExchangeResource::collection($paginator->items()),
            'meta' => LengthAwarePaginatorMetaResource::make($paginator),
        ]);
    }
}
