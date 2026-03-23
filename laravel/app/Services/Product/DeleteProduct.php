<?php

namespace App\Services\Product;

use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Services\Product\Rules\ProductMustNotBeDeleted;

class DeleteProduct
{
    public function __construct(
        private ProductInterface $repository,
        private ProductMustNotBeDeleted $productMustNotBeDeleted,
    ) {}

    /**
     * Execute use case.
     * Accepts FormRequest or array-like data.
     */
    public function execute($request): bool
    {
        $data = is_array($request) ? $request : $request->all();
        $id = $data['id'] ?? ($request->id ?? null);

        if (empty($id)) {
            throw new ResourceNotFoundException('Product id not provided', []);
        }

        $product = $this->repository->findById((int) $id);

        if (empty($product)) {
            throw new ResourceNotFoundException('Product not found', ['Product with id ' . $id . ' not found.']);
        }

        $this->productMustNotBeDeleted->validate($product);

        $this->repository->softDelete((int) $id);

        return true;
    }
}
