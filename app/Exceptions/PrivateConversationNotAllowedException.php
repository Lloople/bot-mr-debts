<?php

namespace App\Exceptions;

use Exception;

class PrivateConversationNotAllowedException extends Exception
{
    public function render()
    {
        return response('Cannot continue in a private conversation');
    }

}
