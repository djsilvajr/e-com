<?php

namespace App\Repository;

use App\Repository\Contracts\PriceInterface;
use App\Models\PriceModel;
use App\Exceptions\PersistenceErrorException;

class PriceRepository implements PriceInterface
{
    public function findByProductId(int $productId)
    {
        $price = PriceModel::where('product_id', $productId)->first();
        return $price ? $price->toArray() : null;
    }

    public function create(array $data)
    {
        try {
            $price = PriceModel::create($data);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $price ? $price->toArray() : null;
    }

    public function update(int $productId, array $data)
    {
        try {
            $price = PriceModel::where('product_id', $productId)->first();
            if (!$price) {
                return null;
            }
            $price->fill($data);
            $price->save();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $price ? $price->toArray() : null;
    }

    public function deleteByProductId(int $productId)
    {
        try {
            $price = PriceModel::where('product_id', $productId)->first();
            if (!$price) {
                return null;
            }
            $price->delete();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return true;
    }
}
