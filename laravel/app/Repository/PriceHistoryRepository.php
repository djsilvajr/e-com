<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exceptions\PersistenceErrorException;
use App\Models\PriceHistoryModel;
use App\Repository\Contracts\PriceHistoryInterface;

class PriceHistoryRepository implements PriceHistoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        try {
            $record = PriceHistoryModel::create($data);
        } catch (\Throwable $e) {
            throw new PersistenceErrorException();
        }

        return $record->toArray();
    }
}
