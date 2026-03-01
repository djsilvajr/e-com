<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\GetProductTypeByIdRequest;
use App\Http\Requests\GetChildProductTypesByIdRequest;
use App\Http\Requests\CreateChildProductType;

use App\Services\ProductTypeService;
use Illuminate\Http\JsonResponse;

class ProductTypeController extends Controller
{
    protected ProductTypeService $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function getProductTypes() : JsonResponse
    {
        $productTypes = $this->productTypeService->getProductTypes();

        return response()->json([
            'status' => true,
            'message' => 'Product types fetched successfully.',
            'errors' => [],
            'data' => $productTypes,
            '_links' => [
                'self' => [
                    'href' => url('v1/product/types'),
                    'method' => 'GET'
                ],
                'GET' => [
                    'href' => url('v1/product/type/{id}'),
                    'method' => 'GET'
                ],
                'GET_CHILD' => [
                    'href' => url('v1/product/type/{id}/child'),
                    'method' => 'GET'
                ],
            ]
        ]);
    }

    public function getProductTypeById($id, Request $request) : JsonResponse
    {
        //$credentials = $request->only(['']);
        $credentials = [];
        $credentials = array_merge($credentials, ['id' => $id]);

        GetProductTypeByIdRequest::validate($credentials);

        $productType = $this->productTypeService->getProductTypeById($id);

        return response()->json([
            'status' => true,
            'message' => 'Product type fetched successfully.',
            'errors' => [],
            'data' => $productType,
            '_links' => [
                'self' => [
                    'href' => url("v1/product/type/{$id}"),
                    'method' => 'GET'
                ],
                'GET_ALL' => [
                    'href' => url('v1/product/types'),
                    'method' => 'GET'
                ],
                'GET_CHILD' => [
                    'href' => url("v1/product/type/{$id}/child"),
                    'method' => 'GET'
                ],
            ]
        ]);
    }

    public function getChildProductTypesById($id, Request $request) : JsonResponse
    {
        //$credentials = $request->only(['']);
        $credentials = [];
        $credentials = array_merge($credentials, ['id' => $id]);

        GetChildProductTypesByIdRequest::validate($credentials);

        $childProductTypes = $this->productTypeService->getChildProductTypesById($id);

        return response()->json([
            'status' => true,
            'message' => 'Child product types fetched successfully.',
            'errors' => [],
            'data' => $childProductTypes,
            '_links' => [
                'self' => [
                    'href' => url("v1/product/type/{$id}/child"),
                    'method' => 'GET',
                ],
                'GET_TYPE' => [
                    'href' => url("v1/product/type/{$id}"),
                    'method' => 'GET',
                ],
                'GET_ALL' => [
                    'href' => url('v1/product/types'),
                    'method' => 'GET'
                ],
            ]
        ]);
    }


    public function createChildProductType($id, Request $request) : JsonResponse
    {
        //$credentials = [];
        $credentials = $request->only(['name', 'slug', 'description', 'variant_type']);
        $credentials['id'] = $id ?? 0;

        CreateChildProductType::validate($credentials);

        $created = $this->productTypeService->createChildProductType($credentials);
        return response()->json([
            'status' => true,
            'message' => 'Child product types created successfully.',
            'errors' => [],
            'data' => $created,
            '_links' => [
                'self' => [
                    'href' => url("v1/product/type/{$id}/child"),
                    'method' => 'POST'
                ],
                'GET_CHILD' => [
                    'href' => url("v1/product/type/{$id}/child"),
                    'method' => 'GET'
                ],
                'GET_TYPE' => [
                    'href' => url("v1/product/type/{$id}"),
                    'method' => 'GET'
                ],
                'GET_ALL' => [
                    'href' => url('v1/product/types'),
                    'method' => 'GET'
                ],
            ]
        ], 201);
    }

}
