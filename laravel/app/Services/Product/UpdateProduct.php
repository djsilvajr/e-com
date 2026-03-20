<?php

namespace App\Services\Product;

use App\Repository\Contracts\ProductInterface;
use App\Repository\Contracts\ProductTypeInterface;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;
use App\Services\Product\Rules\AvailableAtFutureOrTodayRule;
use App\Services\Product\Rules\ProductMustNotBeDeleted;
use App\Services\ProductType\Rules\ProductTypeNeedAParentRule;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\PersistenceErrorException;
use App\Helpers\ArrayHelper;

class UpdateProduct
{
    public function __construct(
        private ProductInterface $productRepository,
        private ProductTypeInterface $productTypeRepository,
        private ProductTypeMustNotBeDeleted $productTypeMustNotBeDeleted,
        private ProductMustNotBeDeleted $productMustNotBeDeleted,
        private AvailableAtFutureOrTodayRule $availableAtRule,
        private ProductTypeNeedAParentRule $productTypeNeedAParent
    ) {}

    public function execute($id, $request): array
    {
        $data = is_array($request) ? $request : $request->all();
        $productTypeId = $data['product_type_id'] ?? null;
        $sku = $data['sku'] ?? null;
        $availableAt = $data['available_at'] ?? null;

        if (!$id) {
            throw new ResourceNotFoundException('Product id not provided', []);
        }

        //Product
        $product = $this->productRepository->findById($id);
        if (empty($product)) {
            throw new ResourceNotFoundException('Product not found', ['Product with id ' . $id . ' not found.']);
        }

        $existing = null;
        if (!empty($sku)) {
            $existing = $this->productRepository->findProductBySku($sku);
        }

        
        if (!empty($existing)) {
            $originalProductId = ArrayHelper::getValue($existing, 'id');
            if ($originalProductId != $id) {
                throw new BusinessRuleException('Product with same SKU already exists', ['sku' => $sku]);
            }
        }

        $this->productMustNotBeDeleted->validate($product);
        $this->availableAtRule->validate($availableAt);

        //Product Type
        $productType = $this->productTypeRepository->findProductTypeById((int) $productTypeId);
        if (empty($productType)) {
            throw new ResourceNotFoundException('Product type not found', ['Product type with id ' . $productTypeId . ' not found.']);
        }

        $productTypeFirst = ArrayHelper::getFirstArrayFromList($productType);
    
        $this->productTypeMustNotBeDeleted->validate($productTypeFirst);
        $this->productTypeNeedAParent->validate($productTypeFirst);



        //Update
        $updated = $this->productRepository->updateProduct($id, $data);

        return [
            'id' => $updated['id'] ?? ($updated->id ?? $id),
            'name' => $updated['name'] ?? ($updated->name ?? ($data['name'] ?? null)),
            'active' => $updated['active'] ?? ($updated->active ?? ($data['active'] ?? null)),
        ];
    }
}
