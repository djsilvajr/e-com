<?php

namespace App\Repository\Contracts;

interface ProductVariantInterface
{
    public function findById(int $id);
    public function findByProductIdAndId(int $productId, int $id);
    public function findBySku(string $sku);
    public function findByBarcode(string $barcode);
    public function findByProductIdAndOrder(int $productId, int $order);
    public function create(array $data);
    public function update(int $id, array $data);
    public function deleteById(int $id);
}
