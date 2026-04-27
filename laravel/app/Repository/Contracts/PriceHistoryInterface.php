<?php

declare(strict_types=1);

namespace App\Repository\Contracts;

interface PriceHistoryInterface
{
    /**
     * Persist a new price history row.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array;
}
