<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProductService;

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
            'links' => [
                'self' => url("/v1/product/{$id}"),
                'delete' => url("/v1/product/{$id}"),
                'update' => url("/v1/product/{$id}"),
                'post' => url("/v1/product"),
            ]
        ]);
    }

}
