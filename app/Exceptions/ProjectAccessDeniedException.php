<?php

namespace App\Exceptions;

class ProjectAccessDeniedException extends ApiException
{
    protected $message = 'You are not allowed to access this project.';
    protected int $status = 403;
}
