<?php

namespace App\Services\ProductType;

use App\Repository\Contracts\ProductTypeInterface;
use App\Exceptions\ResourceNotFoundException;
use App\Helpers\ArrayHelper;
use App\Services\ProductType\Rules\ProductTypeMustNotBeDeleted;

class ChangeProductTypeActivationStatus {

    public function __construct(
        private ProductTypeInterface $productTypeInterface,
        private ProductTypeMustNotBeDeleted $productTypeMustNotBeDeletedRule
    ) {}

    public function execute(array $request) : bool
    {
        $id = $request['id'];
        $status = $request['status'];

        $productType = $this->productTypeInterface->findProductTypeById($id);

        if(empty($productType)) {
            throw new ResourceNotFoundException('Parent id not found', ['There is no parent with id '.$id.'.']);
        }

        $productType = ArrayHelper::getFirstArrayFromList($productType);

        // Valida se o produto não foi deletado
        $this->productTypeMustNotBeDeletedRule->validate($productType);

        // Converte o status string ('TRUE'/'FALSE') para boolean
        $boolStatus = ($status === 'TRUE');

        return $this->productTypeInterface->updateProductTypeStatus($id, $boolStatus);
    }
}
