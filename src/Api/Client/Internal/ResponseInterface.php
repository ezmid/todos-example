<?php

declare(strict_types=1);

namespace App\Api\Client\Internal;

/**
 * Common API Response definition point
 */
interface ResponseInterface
{
    /**
     * Is the response OK?
     */
    public function isOk(): bool;

    /**
     * Get an error description from the response
     */
    public function getErrorMessage(): string;

    /**
     * Get the content
     */
    public function getContent(): ?array;
}
