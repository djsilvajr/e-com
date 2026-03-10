<?php

namespace App\Repository\Contracts;

interface ProductTypeInterface
{
    public function getAllProductTypes() : array;
    public function findProductTypeById(int $id) : array;
    public function findChildProductTypesById(int $id) : array;
    public function insertVariantType(string $name, string $slug, string $description, int $parent_id, string $variantType) : array;
}
