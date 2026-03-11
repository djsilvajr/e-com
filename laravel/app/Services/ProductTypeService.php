<?php

namespace App\Services;

use App\Http\Requests\CreateChildProductType as RequestsCreateChildProductType;
use App\Services\ProductType\GetProductTypes;
use App\Services\ProductType\GetProductTypeById;
use App\Services\ProductType\GetChildProductTypesById;
use App\Services\ProductType\CreateChildProductType;
use App\Services\ProductType\ChangeProductTypeActivationStatus;

class ProductTypeService
{
    public function __construct(
        private GetProductTypes $getProductTypes,
        private GetProductTypeById $getProductTypeById,
        private GetChildProductTypesById $getChildProductTypesById,
        private CreateChildProductType $createChildProductType,
        private ChangeProductTypeActivationStatus $changeProductTypeActivationStatus
    ) {}

    public function getProductTypes(): array
    {
        return $this->getProductTypes->execute();
    }

    public function getProductTypeById(int $id): array
    {
        return $this->getProductTypeById->execute($id);
    }

    public function getChildProductTypesById(int $id): array
    {
        return $this->getChildProductTypesById->execute($id);
    }

    public function createChildProductType(array $request) : array
    {
        return $this->createChildProductType->execute($request);
    }

    public function changeProductTypeActivationStatus(array $request) : bool
    {
        return $this->changeProductTypeActivationStatus->execute($request);
    }
}
