<?php

namespace Wouaka\Exceptions;

/**
 * Exception levée quand la clé API est invalide ou expirée
 */
class InvalidAPIKeyException extends WouakaAPIException
{
    public function __construct(string $message = 'Clé API invalide ou expirée')
    {
        parent::__construct($message, 401);
    }
}
