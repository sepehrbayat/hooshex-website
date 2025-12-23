<?php

declare(strict_types=1);

namespace App\Http\Controllers\Commerce;

use App\Domains\Commerce\Services\Cart;
use App\Http\Requests\Commerce\SyncCartRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    public function sync(SyncCartRequest $request, Cart $cart): JsonResponse
    {
        $data = $request->validated();

        $cart->syncFromLocalStorage($data['items'] ?? []);

        return response()->json([
            'ok' => true,
            'items' => $cart->toArray(),
            'count' => $cart->count(),
            'total' => $cart->total(),
        ]);
    }
}

