<?php

declare(strict_types=1);

namespace App\Api\Message;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * List response
 */
class ListResponse implements MessageInterface
{
    /**
     * @Groups({"api"})
     * @var int
     */
    private $page;

    /**
     * @Groups({"api"})
     * @var int
     */
    private $limit;

    /**
     * @Groups({"api"})
     * @var array
     */
    private $filter;

    /**
     * @Groups({"api"})
     * @var array
     */
    private $orderBy;

    /**
     * @Groups({"api"})
     * @var int
     */
    private $count;

    /**
     * @Groups({"api"})
     * @var int
     */
    private $numOfPages;

    /**
     * @Groups({"api"})
     * @var array
     */
    private $items;

    /**
     * Get the page
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * Set the page
     */
    public function setPage(int $page): ListResponse
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get the limit
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Set the limit
     */
    public function setLimit(int $limit): ListResponse
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the pages
     */
    public function getNumOfPages(): ?int
    {
        return $this->numOfPages;
    }

    /**
     * Set the number of pages
     */
    public function setNumOfPages(int $pages): ListResponse
    {
        $this->numOfPages = $pages;
        return $this;
    }

    /**
     * Get the count
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * Set the count
     */
    public function setCount(int $count): ListResponse
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Get the items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the items
     */
    public function setItems($items): ListResponse
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get the filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set the filter
     */
    public function setFilter($filter): ListResponse
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Get the order by
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set the order by
     */
    public function setOrderBy(array $orderBy): ListResponse
    {
        $this->orderBy = $orderBy;
        return $this;
    }
}
