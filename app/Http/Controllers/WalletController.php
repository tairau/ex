<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Data\Wallet\OpenWallet;
use App\Http\Resources\LengthAwarePaginatorMetaResource;
use App\Http\Resources\WalletResource;
use App\Services\Wallet\WalletService;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly ResponseFactory $response,
        private readonly WalletService $walletService,
    ) {
    }

    public function all(): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $paginator = $this->walletService->paginateForUser($user);

        return $this->response->json([
            'data' => WalletResource::collection($paginator->items()),
            'meta' => LengthAwarePaginatorMetaResource::make($paginator)
        ]);
    }

    /**
     * @param \App\Data\Wallet\OpenWallet $wallet
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function open(OpenWallet $wallet): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $wallet = $this->walletService->open($user, $wallet);

        return $this->response->json([
            'data' => WalletResource::make($wallet->unsetRelation('user')),
        ]);
    }
}
