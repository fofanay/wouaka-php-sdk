<?php

namespace Wouaka\Exceptions;

/**
 * Exception levée quand un document est invalide (KYC)
 */
class InvalidDocumentException extends WouakaAPIException
{
    private ?string $rejectionReason;
    
    public function __construct(
        string $message = 'Document invalide',
        ?string $rejectionReason = null
    ) {
        parent::__construct($message, 400);
        $this->rejectionReason = $rejectionReason;
    }
    
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }
}
