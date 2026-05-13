<?php

namespace App\Repository;

use App\Repository\Contracts\ProductInterface;
use App\Models\ProductModel;
use App\Exceptions\PersistenceErrorException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function updateProduct(int $id, array $data)
    {
        try {
            $product = ProductModel::find($id);
            if (!$product) {
                return null;
            }

            $product->fill($data);
            $product->save();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $product ? $product->toArray() : null;
    }

    public function softDelete(int $id)
    {
        try {
            $product = ProductModel::find($id);
            if (!$product) {
                return null;
            }

            $product->deleted_at = Carbon::now();
            $product->save();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return true;
    }

    public function get(int $product_type_id = 0, $name = '')
    {
        try {
            $products = ProductModel::findByVariantType(
                $product_type_id,
                $name
            );

            if ($products->isEmpty()) {
                return null;
            }

        } catch (\Throwable $e) {
            throw new PersistenceErrorException(
                $e->getMessage()
            );
        }

        return $products->toArray();
    }

    /**
     * Search products by:
     *   - product_type_id (exact match) when > 0; OR
     *   - variant_type (root category — joins product_types.variant_type)
     *     when product_type_id is empty.
     * Optionally filters by name LIKE %name%.
     */
    public function getFiltered(string $variantType, ?int $productTypeId, string $name): array
    {
        try {
            $query = ProductModel::query();

            if ($productTypeId !== null && $productTypeId > 0) {
                $query->where('product_type_id', $productTypeId);
            } elseif ($variantType !== '') {
                $query->whereIn('product_type_id', function ($sub) use ($variantType) {
                    $sub->select('id')
                        ->from('product_types')
                        ->where('variant_type', $variantType);
                });
            } else {
                // Nothing to filter by — return empty to avoid accidental full scan.
                return [];
            }

            if ($name !== '') {
                $query->where('name', 'LIKE', "%{$name}%");
            }

            $products = $query->orderBy('name')->get();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException($e->getMessage());
        }

        return $products ? $products->toArray() : [];
    }
}
