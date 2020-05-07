<?php

declare(strict_types=1);

namespace App\Api\Message\Todo;

use App\Api\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Create a todo entity
 */
class CreateMessage implements MessageInterface
{

    /**
     * @Assert\NotBlank
     * @Groups({"api"})
     */
    public $name;

    /**
     * @Assert\NotBlank
     * @Groups({"api"})
     */
    public $description;

    /**
     * Simple factory for message
     */
    public static function factory(array $data): CreateMessage
    {
        return (new static())
            ->setName($data['name'])
            ->setDescription($data['description'])
        ;
    }

    /**
     * Set the name
     */
    public function setName(string $name): CreateMessage
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
    public function setDescription(string $description): CreateMessage
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
