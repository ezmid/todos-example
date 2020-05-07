<?php

declare(strict_types=1);

namespace App\Api\Message;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This is a classic identification message used for example
 * in a /api/todo REST API.
 */
class Identification implements IdentificationMessageInterface
{
    /**
     * Internal ID that should be used only for internal requests. For public
     * usage use the UUID version of the message.
     *
     * @Groups({"api"})
     */
    protected $uuid;

    /**
     * @inheritDoc
     */
    public function getUUID(): ?string
    {
        return $this->uuid;
    }

    /**
     * @inheritDoc
     */
    public function setUUID(string $uuid): IdentificationMessageInterface
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->getUUID());
    }

    /**
     * @inheritDoc
     */
    public function getFindByOptions(): array
    {
        if (!empty($this->uuid)) {
            return [
                'uuid' => $this->uuid
            ];
        }

        return [];
    }
}
