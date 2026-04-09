<?php

namespace App\Exceptions;

class ProjectStatusTransitionException extends ApiException
{
    protected $message = 'Invalid project status transition.';
    protected int $status = 422;
}
