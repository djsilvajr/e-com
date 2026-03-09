<?php


namespace App\Helpers;

use App\Exceptions\BadRequestException;
use App\Exceptions\PersistenceErrorException;

class ArrayHelper
{
    /**
     * Returns the first element of a list as an array.
     * Throws PersistenceErrorException if array is empty or first element is missing.
     */
    public static function getFirstArrayFromList(array $array): ?array
    {
        if (empty($array) || !isset($array[0])) {
            throw new PersistenceErrorException();
        }

        return (array) $array[0];
    }

    /**
     * Safely get first element without throwing.
     * Returns null if array is empty.
     */
    public static function getFirstOrNull(array $array): ?array
    {
        if (empty($array) || !isset($array[0])) {
            return null;
        }

        return (array) $array[0];
    }
}
