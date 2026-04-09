<?php

namespace App\Exceptions;

class ProjectNotFoundException extends ApiException
{
    protected $message = 'Project not found.';
    protected int $status = 404;
}
