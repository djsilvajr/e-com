<?php

namespace App\Services\ProductVariant;

use App\Repository\Contracts\ProductVariantInterface;
use App\Repository\Contracts\ProductInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\DuplicatedValueException;
use App\Enums\ProductVariantType;

class UpdateProductVariant
{
    public function __construct(
        private ProductVariantInterface $productVariantRepository,
        private ProductInterface $productRepository,
    ) {}

    public function execute(int $productId, int $id, array $data): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $variant = $this->productVariantRepository->findByProductIdAndId($productId, $id);
        if (!$variant) {
            throw new ResourceNotFoundException('Product variant not found');
        }

        $sku = $data['sku'] ?? null;
        if ($sku !== null) {
            $existingBySku = $this->productVariantRepository->findBySku($sku);
            if ($existingBySku && (int) $existingBySku['id'] !== $id) {
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
            if ($existingByBarcode && (int) $existingByBarcode['id'] !== $id) {
                throw new DuplicatedValueException('Barcode already in use');
            }
        }

        if (array_key_exists('variant_type', $data)) {
            $variantType = $data['variant_type'];
            if ($variantType === null || ProductVariantType::tryFrom($variantType) === null) {
                throw new BusinessRuleException('Invalid variant type');
            }
        }

        if (array_key_exists('dimensions', $data) && is_array($data['dimensions'])) {
            $data['dimensions'] = [
                'altura'       => $data['dimensions']['altura'] ?? null,
                'largura'      => $data['dimensions']['largura'] ?? null,
                'profundidade' => $data['dimensions']['profundidade'] ?? null,
            ];
        }

        if (array_key_exists('order', $data) && $data['order'] !== null) {
            $newOrder = (int) $data['order'];
            $currentOrder = (int) $variant['order'];

            if ($newOrder !== $currentOrder) {
                $conflict = $this->productVariantRepository->findByProductIdAndOrder($productId, $newOrder);
                if ($conflict && (int) $conflict['id'] !== $id) {
                    $this->productVariantRepository->update((int) $conflict['id'], [
                        'order' => $currentOrder,
                    ]);
                }
            }
        }

        $data['product_id'] = $productId;

        return $this->productVariantRepository->update($id, $data);
    }
}
