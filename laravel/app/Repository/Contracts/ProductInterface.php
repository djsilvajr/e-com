<?php

namespace App\Repository\Contracts;

interface ProductInterface
{
    public function findById(int $id);
    public function findProductBySku(string $sku);
    public function createProduct(array $data);
    public function updateProduct(int $id, array $data);
}
