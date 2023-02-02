<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Services\Currency\CurrencyService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly CurrencyService $currencyService,
    ) {
    }

    public function all(): Response
    {
        $currencies = $this->currencyService->all();

        return $this->response->json([
            'data' => CurrencyResource::collection($currencies),
        ]);
    }
}
