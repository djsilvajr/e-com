<?php

namespace App\Services\Product;

use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use App\Services\Product\Rules\AvailableAtFutureOrTodayRule;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\PersistenceErrorException;
use PHPUnit\Event\Code\Throwable;
use App\Helpers\ArrayHelper;
use App\Services\ProductType\Rules\ProductTypeNeedAParentRule;

class CreateProduct
{
    public function __construct(
        private ProductInterface $productRepository,
        private ProductTypeInterface $productTypeRepository,
        private ProductTypeMustNotBeDeleted $productTypeMustNotBeDeleted,
        private AvailableAtFutureOrTodayRule $availableAtRule,
        private ProductTypeNeedAParentRule $productTypeNeedAParent
    ) {}

    /**
     * Execute use case.
     * Accepts either FormRequest or array-like data.
     */
    public function execute($request): array
    {
        $data = is_array($request) ? $request : $request->all();

        $productTypeId = $data['product_type_id'] ?? null;
        $sku = $data['sku'] ?? null;
        $availableAt = $data['available_at'] ?? null;

        $productType = $this->productTypeRepository->findProductTypeById((int) $productTypeId);

        if (empty($productType)) {
            throw new ResourceNotFoundException('Product type not found', ['Product type with id ' . $productTypeId . ' not found.']);
        }

        $existing = null;
        if (!empty($sku)) {
            $existing = $this->productRepository->findProductBySku($sku);
        }

        if (!empty($existing)) {
            throw new BusinessRuleException('Product with same SKU already exists', ['sku' => $sku]);
        }


        $productTypeFirstValue = ArrayHelper::getFirstArrayFromList($productType);

        $this->productTypeMustNotBeDeleted->validate($productTypeFirstValue);
        $this->productTypeNeedAParent->validate($productTypeFirstValue);
        //$this->availableAtRule->validate($availableAt);

        $created = $this->productRepository->createProduct($data);

        return [
            'id' => $created['id'] ?? ($created->id ?? null),
            'name' => $created['name'] ?? ($created->name ?? null),
            'active' => $created['active'] ?? ($created->active ?? null),
        ];
    }
}
