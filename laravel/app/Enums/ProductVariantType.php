<?php

namespace App\Enums;

enum ProductVariantType: string
{
    case Clothing = 'clothing';
    case Electronics = 'electronics';
    case Furniture = 'furniture';
    case Books = 'books';
    case Simple = 'simple';
}
