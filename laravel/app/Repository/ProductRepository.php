<?php

namespace App\Repository;

use App\Repository\Contracts\ProductInterface;
use App\Models\ProductModel;

class ProductRepository implements ProductInterface
{
    public function findById(int $id)
    {
        return ProductModel::find($id);
    }
}
