<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Data\Purchase\Fund;
use App\Services\Purchase\PurchaseRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly AuthManager $authManager,
        private readonly PurchaseRepository $purchaseRepository,
    ) {
    }

    public function add(Fund $fund): Response
    {
        /** @var \App\Models\User $user */
        $user = $this->authManager->user();

        $this->purchaseRepository->add($user, $fund);

        return $this->response->noContent();
    }
}
