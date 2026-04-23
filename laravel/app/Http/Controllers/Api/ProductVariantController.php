<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;
use App\Http\Requests\GetProductVariantRequest;
use App\Http\Requests\AddProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Requests\DeleteProductVariantRequest;

class ProductVariantController extends Controller
{
    public function __construct(
        private ProductVariantService $productVariantService,
    ) {}

    public function getVariant(GetProductVariantRequest $request, int $product_id, int $id)
    {
        $variant = $this->productVariantService->get($product_id, $id);

        return response()->json([
            'status' => true,
            'message' => 'Product variant fetched successfully.',
            'errors' => [],
            'data' => $variant,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/variant/"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }

    public function addVariant(AddProductVariantRequest $request, int $product_id)
    {
        $variant = $this->productVariantService->add($product_id, $request->all());

        $id = $variant['id'] ?? null;

        return response()->json([
            'status' => true,
            'message' => 'Product variant created successfully.',
            'errors' => [],
            'data' => $variant,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/variant/"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'DELETE',
                ],
            ],
        ], 201);
    }

    public function updateVariant(UpdateProductVariantRequest $request, int $product_id, int $id)
    {
        $variant = $this->productVariantService->update($product_id, $id, $request->all());

        return response()->json([
            'status' => true,
            'message' => 'Product variant updated successfully.',
            'errors' => [],
            'data' => $variant,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/variant/"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }

    public function deleteVariant(DeleteProductVariantRequest $request, int $product_id, int $id)
    {
        $this->productVariantService->delete($product_id, $id);

        return response()->json([
            'status' => true,
            'message' => 'Product variant deleted successfully.',
            'errors' => [],
            'data' => null,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'GET',
                ],
                'create' => [
                    'href' => url("/v1/product/{$product_id}/variant/"),
                    'method' => 'POST',
                ],
                'update' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'PUT',
                ],
                'delete' => [
                    'href' => url("/v1/product/{$product_id}/variant/{$id}"),
                    'method' => 'DELETE',
                ],
            ],
        ]);
    }
}
