<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Services\Currency\CurrencyRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public function all(): Response
    {
        $currencies = $this->currencyRepository->all();

        return $this->response->json([
            'data' => CurrencyResource::collection($currencies),
        ]);
    }
}
