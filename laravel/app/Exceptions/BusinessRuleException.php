<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    protected $errors;

    public function __construct(string $message = 'Business rule violation', array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function render($request)
    {
        return response()->json([
            'status' => false,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
            'data' => []
        ], 422);
    }
}
