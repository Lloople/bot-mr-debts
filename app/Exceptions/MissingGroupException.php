<?php

namespace App\Exceptions;

use Exception;

class MissingGroupException extends Exception
{
    public function render()
    {
        return response('Cannot continue without group');
    }

}
