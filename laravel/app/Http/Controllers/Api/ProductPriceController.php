<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PriceService;
use App\Http\Requests\GetPriceRequest;
use App\Http\Requests\AddPriceRequest;
use App\Http\Requests\UpdatePriceRequest;
use App\Http\Requests\DeletePriceRequest;

class ProductPriceController extends Controller
{
    public function __construct(
        private PriceService $priceService,
    ) {}

    public function getPrice(GetPriceRequest $request, int $product_id)
    {
        $price = $this->priceService->get($product_id);

        return response()->json([
            'status' => true,
            'message' => 'Price fetched successfully.',
            'errors' => [],
            'data' => $price,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }

    public function addPrice(AddPriceRequest $request, int $product_id)
    {
        $price = $this->priceService->add($product_id, $request->all());

        return response()->json([
            'status' => true,
            'message' => 'Price created successfully.',
            'errors' => [],
            'data' => $price,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'DELETE',
                ],
            ],
        ], 201);
    }

    public function updatePrice(UpdatePriceRequest $request, int $product_id)
    {
        $price = $this->priceService->update($product_id, $request->all());

        return response()->json([
            'status' => true,
            'message' => 'Price updated successfully.',
            'errors' => [],
            'data' => $price,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }

    public function deletePrice(DeletePriceRequest $request, int $product_id)
    {
        $this->priceService->delete($product_id);

        return response()->json([
            'status' => true,
            'message' => 'Price deleted successfully.',
            'errors' => [],
            'data' => null,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/price"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }
}
