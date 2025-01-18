<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    public function __construct(string $message = 'Validation error', int $statusCode = 400)
    {
        parent::__construct($statusCode, $message);
    }
}
