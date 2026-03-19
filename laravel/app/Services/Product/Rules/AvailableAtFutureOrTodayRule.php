<?php

namespace App\Services\Product\Rules;

use App\Exceptions\BusinessRuleException;
use Carbon\Carbon;

class AvailableAtFutureOrTodayRule
{
    public function validate($available_at): void
    {
        if (empty($available_at)) {
            return;
        }

        try {
            $dt = Carbon::parse($available_at)->timezone('UTC');
        } catch (\Throwable $e) {
            throw new BusinessRuleException('Invalid date for available_at', ['available_at' => $available_at]);
        }

        $today = Carbon::today('UTC');

        if ($dt->lt($today)) {
            throw new BusinessRuleException('available_at must be today or a future date', ['available_at' => $available_at]);
        }
    }
}
