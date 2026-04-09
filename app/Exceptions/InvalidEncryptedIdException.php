<?php

namespace App\Exceptions;

class InvalidEncryptedIdException extends ApiException
{
    protected $message = 'Invalid identifier supplied.';
    protected int $status = 400;
}
