<?php

namespace Wouaka\Exceptions;

use Exception;

/**
 * Exception de base pour toutes les erreurs API Wouaka
 */
class WouakaAPIException extends Exception
{
    protected ?int $statusCode;
    protected ?array $response;
    
    public function __construct(
        string $message,
        ?int $statusCode = null,
        ?array $response = null
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->response = $response;
    }
    
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
    
    public function getResponse(): ?array
    {
        return $this->response;
    }
}
