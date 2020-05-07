<?php declare(strict_types=1);

namespace App\Api\Message;

/**
 * Used to identify different types of entities in API calls
 */
interface IdentificationMessageInterface extends MessageInterface
{
    /**
     * Get the ID attribute
     */
    public function getUUID(): ?string;

    /**
     * Set the ID attribute
     */
    public function setUUID(string $uuid): IdentificationMessageInterface;

    /**
     * Is the incomming message empty
     */
    public function isEmpty(): bool;

    /**
     * Will return an array of options for the Doctrine $repo->findBy() method
     */
    public function getFindByOptions(): array;
}
