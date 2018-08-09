<?php

namespace App\Exceptions;

use Exception;

class InteractingWithBotException extends Exception
{
    public function render()
    {
        return response('Cannot continue if talking to bot');
    }

}
