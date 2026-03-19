<?php

namespace App\Repository;

use App\Repository\Contracts\ProductInterface;
use App\Models\ProductModel;
use App\Exceptions\PersistenceErrorException;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductInterface
{
    public function findById(int $id)
    {
        $product = ProductModel::find($id);
        return $product ? $product->toArray() : null;
    }

    public function findProductBySku(string $sku)
    {
        try {
            $product = ProductModel::where('sku', $sku)->first();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $product ? $product->toArray() : null;
    }

    public function createProduct(array $data)
    {
        try {
            $created = ProductModel::create($data);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $created ? $created->toArray() : null;
    }
}
