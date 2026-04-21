<?php

namespace App\Services;

use App\Services\Price\GetPrice;
use App\Services\Price\AddPrice;
use App\Services\Price\UpdatePrice;
use App\Services\Price\DeletePrice;

class PriceService
{
    public function __construct(
        private GetPrice $getPrice,
        private AddPrice $addPrice,
        private UpdatePrice $updatePrice,
        private DeletePrice $deletePrice,
    ) {}

    public function get(int $productId): array
    {
        return $this->getPrice->execute($productId);
    }

    public function add(int $productId, array $data): array
    {
        return $this->addPrice->execute($productId, $data);
    }

    public function update(int $productId, array $data): array
    {
        return $this->updatePrice->execute($productId, $data);
    }

    public function delete(int $productId): bool
    {
        return $this->deletePrice->execute($productId);
    }
}
