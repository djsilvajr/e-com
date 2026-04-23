<?php

namespace App\Services\ProductVariant;

use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\DuplicatedValueException;
use App\Enums\ProductVariantType;

class AddProductVariant
{
    public function __construct(
        private ProductVariantInterface $productVariantRepository,
        private ProductInterface $productRepository,
    ) {}

    public function execute(int $productId, array $data): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $sku = $data['sku'] ?? null;
        if ($sku !== null) {
            $existingBySku = $this->productVariantRepository->findBySku($sku);
            if ($existingBySku) {
                throw new DuplicatedValueException('SKU already in use');
            }

            $productBySku = $this->productRepository->findProductBySku($sku);
            if ($productBySku) {
                throw new DuplicatedValueException('SKU already in use by a product');
            }
        }

        $barcode = $data['barcode'] ?? null;
        if ($barcode !== null) {
            $existingByBarcode = $this->productVariantRepository->findByBarcode($barcode);
            if ($existingByBarcode) {
                throw new DuplicatedValueException('Barcode already in use');
            }
        }

        $variantType = $data['variant_type'] ?? null;
        if ($variantType === null || ProductVariantType::tryFrom($variantType) === null) {
            throw new BusinessRuleException('Invalid variant type');
        }

        if (array_key_exists('dimensions', $data) && is_array($data['dimensions'])) {
            $data['dimensions'] = [
                'altura'       => $data['dimensions']['altura'] ?? null,
                'largura'      => $data['dimensions']['largura'] ?? null,
                'profundidade' => $data['dimensions']['profundidade'] ?? null,
            ];
        }

        $data['product_id'] = $productId;

        return $this->productVariantRepository->create($data);
    }
}
