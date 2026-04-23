<?php

namespace App\Repository;

use App\Repository\Contracts\ProductVariantInterface;
use App\Models\ProductVariantModel;
use App\Exceptions\PersistenceErrorException;

class ProductVariantRepository implements ProductVariantInterface
{
    public function findById(int $id)
    {
        $variant = ProductVariantModel::find($id);
        return $variant ? $variant->toArray() : null;
    }

    public function findByProductIdAndId(int $productId, int $id)
    {
        $variant = ProductVariantModel::where('product_id', $productId)
            ->where('id', $id)
            ->first();

        return $variant ? $variant->toArray() : null;
    }

    public function findBySku(string $sku)
    {
        try {
            $variant = ProductVariantModel::where('sku', $sku)->first();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $variant ? $variant->toArray() : null;
    }

    public function findByBarcode(string $barcode)
    {
        try {
            $variant = ProductVariantModel::where('barcode', $barcode)->first();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $variant ? $variant->toArray() : null;
    }

    public function findByProductIdAndOrder(int $productId, int $order)
    {
        try {
            $variant = ProductVariantModel::where('product_id', $productId)
                ->where('order', $order)
                ->first();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $variant ? $variant->toArray() : null;
    }

    public function create(array $data)
    {
        try {
            $variant = ProductVariantModel::create($data);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $variant ? $variant->toArray() : null;
    }

    public function update(int $id, array $data)
    {
        try {
            $variant = ProductVariantModel::find($id);
            if (!$variant) {
                return null;
            }
            $variant->fill($data);
            $variant->save();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $variant ? $variant->toArray() : null;
    }

    public function deleteById(int $id)
    {
        try {
            $variant = ProductVariantModel::find($id);
            if (!$variant) {
                return null;
            }
            $variant->delete();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return true;
    }
}
