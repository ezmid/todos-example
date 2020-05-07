<?php

declare(strict_types=1);

namespace App\Api\Client;

/**
 * Generic API interface
 */
interface ApiInterface
{
    /**
     * Get the last recorded error message
     */
    public function getLastErrorMessage(): string;
}
