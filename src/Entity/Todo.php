<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Todo represents one task in the list of todos
 * 
 * @ORM\Entity(
 *      repositoryClass="App\Repository\TodoRepository"
 * )
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *      name="todo",
 * )
 */
class Todo
{
    /**
     * In case we have "dangling status" entities
     * 
     * @var int
     */
    const STATUS_NONE = 0;

    /**
     * The item was just added
     * 
     * @var int
     */
    const STATUS_NEW = 10;

    /**
     * The item was marked as done
     * @var int
     */
    const STATUS_DONE = 20;

    /**
     * @var array
     */
    const MAP_STATUS_TO_LABEL = [
        self::STATUS_NONE => 'none',
        self::STATUS_NEW => 'new',
        self::STATUS_DONE => 'done',
    ];

    /**
     * @var array
     */
    const MAP_STATUS_TO_COLOR_CLASS = [
        self::STATUS_NONE => 'dark',
        self::STATUS_NEW => 'primary',
        self::STATUS_DONE => 'success',
    ];

    /**
     * @var array
     */
    const MAP_STATUS_TO_ICON_CLASS = [
        self::STATUS_NONE => 'fa-skull',
        self::STATUS_NEW => 'fa-running',
        self::STATUS_DONE => 'fa-check-circle',
    ];

    /**
     * Simple status -> label mapper
     */
    public static function mapStatusToLabel(int $status): string
    {
        // Check for key
        if (isset(static::MAP_STATUS_TO_LABEL[$status])) {
            return static::MAP_STATUS_TO_LABEL[$status];
        }

        return '';
    }

    /**
     * Simple status -> color class mapper
     */
    public static function mapStatusToColorClass(int $status): string
    {
        // Check for key
        if (isset(static::MAP_STATUS_TO_COLOR_CLASS[$status])) {
            return static::MAP_STATUS_TO_COLOR_CLASS[$status];
        }

        return '';
    }

    /**
     * Simple status -> icon class mapper
     */
    public static function mapStatusToIconClass(int $status): string
    {
        // Check for key
        if (isset(static::MAP_STATUS_TO_ICON_CLASS[$status])) {
            return static::MAP_STATUS_TO_ICON_CLASS[$status];
        }

        return '';
    }

    /**
     * @ORM\Id
     * @ORM\Column(
     *      type="smallint",
     *      nullable=false,
     *      options={
     *          "unsigned"=true
     *      }
     * )
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(
     *      type="guid",
     *      unique=true,
     *      options={
     *          "comment": "Public unique identifier for the todo"
     *      }
     * )
     * @Groups({"api"})
     * @var string
     */
    private $uuid;

    /**
     * @ORM\Column(
     *      type="string",
     *      length=32,
     *      name="name",
     *      nullable=false,
     *      options={
     *          "comment": "Name of the todo"
     *      }
     * )
     * @Assert\NotBlank()
     * @Groups({"api"})
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(
     *      type="string",
     *      length=255,
     *      name="description",
     *      nullable=false,
     *      options={
     *          "comment": "Description of the todo"
     *      }
     * )
     * @Assert\NotBlank()
     * @Groups({"api"})
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="time_created",
     *      nullable=false
     * )
     * @Assert\Type("DateTimeInterface")
     * @Groups({"api"})
     * @var DateTimeInterface
     */
    private $timeCreated;

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="time_updated",
     *      nullable=true
     * )
     * @Assert\Type("DateTimeInterface")
     * @Groups({"api"})
     * @var DateTimeInterface
     */
    private $timeUpdated;

    /**
     * @ORM\Column(
     *      type="smallint",
     *      nullable=false,
     *      options={
     *          "default": 0,
     *          "unsigned": true,
     *          "comment": "0 - Default non process value, 10 - New, 20 - Done"
     *      }
     * )
     * @Groups({"api"})
     * @var int
     */
    private $status = self::STATUS_NEW;

    /**
     * Get the id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the id
     */
    public function setId(int $id): Todo
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the unique id
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Set the unique id
     */
    public function setUuid(string $uuid): Todo
    {
        $this->uuid = $uuid;
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
     * Set the names
     */
    public function setName(string $name): Todo
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get the description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description
     */
    public function setDescription(string $description): Todo
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the time of creation
     */
    public function getTimeCreated(): ?DateTimeInterface
    {
        return $this->timeCreated;
    }

    /**
     * Get the time of the last update
     */
    public function getTimeUpdated(): ?DateTimeInterface
    {
        return $this->timeUpdated;
    }

    /**
     * Get the status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the status
     */
    public function setStatus(int $status): Todo
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get a human readable status label
     *
     * @Groups({"api"})
     */
    public function getStatusLabel(): string
    {
        return static::mapStatusToLabel($this->getStatus());
    }

    /**
     * Get a status color class
     *
     * @Groups({"api"})
     */
    public function getStatusColorClass(): string
    {
        return static::mapStatusToColorClass($this->getStatus());
    }

    /**
     * Get a status icon class
     *
     * @Groups({"api"})
     */
    public function getStatusIconClass(): string
    {
        return static::mapStatusToIconClass($this->getStatus());
    }

    /**
     * Can this todo be marked as done?
     */
    public function canBeDone(): bool
    {
        return in_array($this->status, [
            static::STATUS_NEW
        ]);
    }

    /**
     * Serialization proxy
     *
     * @Groups({"api"})
     */
    public function getCanBeDone(): bool
    {
        return $this->canBeDone();
    }

    /**
     * Can this todo be undone?
     */
    public function canBeNew(): bool
    {
        return in_array($this->status, [
            static::STATUS_DONE
        ]);
    }

    /**
     * Serialization proxy
     *
     * @Groups({"api"})
     */
    public function getCanBeNew(): bool
    {
        return $this->canBeNew();
    }

    /**
     * Ensure entity is valid before persisting to storage
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function onPrePersist(): void
    {
        // UUID
        if (empty($this->uuid)) {
            $this->uuid = (Uuid::uuid4())->toString();
        }

        // Set the current time as creation time
        if (empty($this->timeCreated)) {
            $this->timeCreated = new DateTimeImmutable();
        } else {
            // Always update
            $this->timeUpdated = new DateTimeImmutable();
        }
    }

}
