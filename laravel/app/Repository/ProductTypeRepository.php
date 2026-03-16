<?php

namespace App\Repository;

use App\Repository\Contracts\ProductTypeInterface;
use App\Exceptions\PersistenceErrorException;

use Illuminate\Support\Facades\DB;

use App\Models\ProductTypeModel;
use Carbon\Carbon;

class ProductTypeRepository implements ProductTypeInterface
{

    private ProductTypeModel $productTypeModel;

    public function __construct(ProductTypeModel $productTypeModel)
    {
        $this->productTypeModel = $productTypeModel;
    }


    public function getAllProductTypes(): array
    {
        $productTypes = [];

        try {
            $productTypes = DB::table('product_types')
                ->select('id', 'name', 'slug', 'description', 'order', 'icon', 'image_url', 'active', 'created_at', 'updated_at', 'deleted_at')
                ->where('parent_id', null)
                ->get()
                ->toArray();

        } catch (\Throwable $th) {
            throw new PersistenceErrorException();
        }

        return $productTypes;
    }

    public function findProductTypeById(int $id): array
    {
        $productType = [];

        try {
            $productType = DB::table('product_types')
                ->select('id', 'name', 'slug', 'description', 'parent_id', 'variant_type', 'order', 'icon', 'image_url', 'active', 'created_at', 'updated_at', 'deleted_at')
                ->where('id', $id)
                ->get()
                ->toArray();

        } catch (\Throwable $th) {
            throw new PersistenceErrorException();
        }

        return $productType;
    }

    public function findChildProductTypesById(int $id): array
    {
        $childProductTypes = [];

        try {
            $childProductTypes = DB::table('product_types')
                ->select('id', 'name', 'slug', 'description', 'parent_id', 'variant_type', 'order', 'icon', 'image_url', 'active', 'created_at', 'updated_at', 'deleted_at')
                ->where('parent_id', $id)
                ->get()
                ->toArray();

        } catch (\Throwable $th) {
            throw new PersistenceErrorException();
        }

        $childProductTypes = $childProductTypes ?? [];

        return $childProductTypes;
    }

    public function getAllTypesByVariantType(string $variantType) : ?array {

        $return = [];

        try {
            $return = $this->productTypeModel
                ->newQuery()
                ->select(['id', 'name', 'slug', 'description', 'parent_id', 'variant_type', 'order', 'active'])
                ->ofVariant($variantType) // chama o scope
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $return;
    }

    public function insertVariantType(string $name, string $slug, string $description, int $parent_id, string $variantType) : array {

        try {
            $newProductType = $this->productTypeModel->create([
                'name'         => $name,
                'slug'         => $slug,
                'description'  => $description,
                'parent_id'    => $parent_id > 0 ? $parent_id : null,
                'variant_type' => $variantType,
                'active'       => 0,
            ]);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $newProductType->toArray();
    }

    public function updateProductTypeStatus(int $id, bool $status) : bool {
        try {
            $update = $this->productTypeModel
                ->newQuery()
                ->where('id', $id)
                ->update(['active' => $status]);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $update > 0;
    }

    public function deleteProductTypeById(int $id) : bool {
         try {
            $delete = $this->productTypeModel
                ->newQuery()
                ->where('id', $id)
                ->update([
                    'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'active' => false
                ]);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $delete > 0;
    }

}
