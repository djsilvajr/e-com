<?php

namespace App\Repository\Contracts;

interface PriceInterface
{
    public function findByProductId(int $productId);
    public function create(array $data);
    public function update(int $productId, array $data);
    public function deleteByProductId(int $productId);
}
