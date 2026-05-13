<?php

namespace App\Repository\Contracts;

interface ProductInterface
{
    public function findById(int $id);
    public function get(int $product_type_id, string $name);
    public function getFiltered(string $variantType, ?int $productTypeId, string $name): array;
    public function findProductBySku(string $sku);
    public function createProduct(array $data);
    public function updateProduct(int $id, array $data);
    public function softDelete(int $id);
}
