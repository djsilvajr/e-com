<?php

namespace App\Repository\Contracts;

interface ProductInterface
{
    public function findById(int $id);
}
