<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProductService;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProductById(\App\Http\Requests\GetProductByIdRequest $request, $id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json([
            'status' => true,
            'message' => 'Product fetched successfully.',
            'errors' => [],
            'data' => $product,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'GET'
                ],
                'create' => [
                    'href' => url("/v1/product/"),
                    'method' => 'POST'
                ],
                'delete' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'DELETE'
                ],
                'update' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'PUT'
                ],
            ]
        ]);
    }

    public function createProduct(CreateProductRequest $request)
    {
        $result = $this->productService->createProduct($request);

        $id = $result['id'] ?? null;

        $data = $result;

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully.',
            'errors' => [],
            'data' => $data,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'GET'
                ],
                'create' => [
                    'href' => url("/v1/product/"),
                    'method' => 'POST'
                ],
                'delete' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'DELETE'
                ],
                'update' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'PUT'
                ],
            ]
        ]);
    }

    public function updateProduct(UpdateProductRequest $request, $id)
    {
        $result = $this->productService->updateProduct($id, $request);

        $data = $result;

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully.',
            'errors' => [],
            'data' => $data,
            '_links' => [
                'self' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'GET'
                ],
                'create' => [
                    'href' => url("/v1/product/"),
                    'method' => 'POST'
                ],
                'delete' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'DELETE'
                ],
                'update' => [
                    'href' => url("/v1/product/{$id}"),
                    'method' => 'PUT'
                ],
            ]
        ]);
    }

}
