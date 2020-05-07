<?php

declare(strict_types=1);

namespace App\Api\Message\Todo;

use App\Api\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Update a todo entity
 */
class UpdateMessage implements MessageInterface
{
    /**
     * @Assert\NotBlank
     * @Groups({"api"})
     */
    private $uuid;

    /**
     * @Assert\NotBlank
     * @Groups({"api"})
     */
    private $name;

    /**
     * @Assert\NotBlank
     * @Groups({"api"})
     */
    private $description;

    /**
     * Simple factory for message
     */
    public static function factory(array $data): UpdateMessage
    {
        return (new static())
            ->setUUID($data['uuid'])
            ->setName($data['name'])
            ->setDescription($data['description'])
        ;
    }

    /**
     * Set the uuid
     */
    public function setUUID(string $uuid): UpdateMessage
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Get the uuid
     */
    public function getUUID(): string
    {
        return $this->uuid;
    }

    /**
     * Set the name
     */
    public function setName(string $name): UpdateMessage
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the description
     */
    public function setDescription(string $description): UpdateMessage
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the description
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
