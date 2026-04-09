<?php

namespace App\Exceptions;

class ProjectCollaborationNotAllowedException extends ApiException
{
    protected $message = 'Project collaboration is not available yet.';
    protected int $status = 409;
}
