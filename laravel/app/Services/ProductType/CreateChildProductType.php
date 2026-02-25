<?php

namespace App\Services\ProductType;



class CreateChildProductType {

    public function __construct()
    {

    }

    public function execute(array $request) : array
    {
        print_r($request);
        die;
        return [];
    }
}
