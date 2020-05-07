<?php

declare(strict_types=1);

namespace App\Api\Message;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List options
 */
class ListRequest implements MessageInterface
{
    /**
     * Default limit for listing
     *
     * @var int
     */
    const DEFAULT_LIMIT = 10;

    /**
     * @Groups({"api"})
     */
    private $page = 1;

    /**
     * @Groups({"api"})
     */
    private $limit = self::DEFAULT_LIMIT;

    /**
     * @Groups({"api"})
     */
    private $filter = [];

    /**
     * @Groups({"api"})
     */
    private $orderBy = [];

    /**
     * Create a ListRequest object in a controlled fashion
     */
    public static function factory(array $options): ListRequest
    {
        return (new static())
            ->setFilter(isset($options['filter']) ? $options['filter'] : [])
            ->setPage(isset($options['page']) ? $options['page'] : 1)
            ->setLimit(isset($options['limit']) ? $options['limit'] : 0)
            ->setOrderBy(isset($options['orderBy']) ? $options['orderBy']: [])
        ;
    }

    /**
     * Get the page
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set the page
     */
    public function setPage(int $page): ListRequest
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get the limit
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set the limit
     */
    public function setLimit(int $limit): ListRequest
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the filter
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Set the filter
     */
    public function setFilter($filter): ListRequest
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Get the order by
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * Set the order by
     */
    public function setOrderBy(array $orderBy): ListRequest
    {
        $this->orderBy = $orderBy;
        return $this;
    }
}
