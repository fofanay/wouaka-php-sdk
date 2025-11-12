<?php

namespace Wouaka\Exceptions;

/**
 * Exception levée quand le quota API est dépassé
 */
class QuotaExceededException extends WouakaAPIException
{
    private ?int $quotaLimit;
    private ?int $quotaUsed;
    
    public function __construct(
        string $message = 'Quota API dépassé',
        ?int $quotaLimit = null,
        ?int $quotaUsed = null
    ) {
        parent::__construct($message, 429);
        $this->quotaLimit = $quotaLimit;
        $this->quotaUsed = $quotaUsed;
    }
    
    public function getQuotaLimit(): ?int
    {
        return $this->quotaLimit;
    }
    
    public function getQuotaUsed(): ?int
    {
        return $this->quotaUsed;
    }
}
