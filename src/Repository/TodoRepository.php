<?php

declare(strict_types=1);

namespace App\Repository;

use App\Api\Message\ListRequest;
use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * All the Todo specific selects into this class.
 */
class TodoRepository extends ServiceEntityRepository
{
    /**
     * The name of the entity to filter
     * @var string
     */
    const ENTITY_NAME = 'App:Todo';

    /**
     * The shorcut used in the DQL
     * @var string
     */
    const ENTITY_SHORTCUT = 'r';

    /**
     * In case you are not sure use this
     * @var int
     */
    const DEFAULT_QUERY_LIMIT = 10;

    /**
     * Default field to order by
     * @var string
     */
    const DEFAULT_ORDER_BY_FIELD = 'id';

    /**
     * Default order direction
     * @var string
     */
    const DEFAULT_ORDER_BY_DIRECTION = 'ASC';

    /**
     * Default order by values
     * @var array
     */
    const DEFAULT_ORDER_BY = [
        self::DEFAULT_ORDER_BY_FIELD,
        self::DEFAULT_ORDER_BY_DIRECTION,
    ];

    /**
     * Default filter for the list
     * @var array
     */
    const DEFAULT_FILTER = [];

    /**
     * Init dependencies and configure entity type
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    /**
     * Filter by ListRequest object
     */
    public function filterByListRequest(ListRequest $request): Paginator
    {
        // Init filter
        if (empty($request->getFilter())) {
            $request->setFilter(static::DEFAULT_FILTER);
        }

        // Init page
        if (empty($request->getPage())) {
            $request->setPage(1);
        }

        // Init limit
        if (empty($request->getLimit())) {
            $request->setLimit(static::DEFAULT_QUERY_LIMIT);
        }

        // Init order by
        if (empty($request->getOrderBy())) {
            $request->setOrderBy(static::DEFAULT_ORDER_BY);
        }

        // Proxy to method
        return $this->findAllPaginated(
            $request->getFilter(),
            $request->getPage(),
            $request->getLimit(),
            $request->getOrderBy()
        );
    }

    /**
     * Filter todos paginated
     */
    public function findAllPaginated(
        ?array $filters = self::DEFAULT_FILTER,
        ?int $page = 1,
        ?int $max = self::DEFAULT_QUERY_LIMIT,
        ?array $orderBy = self::DEFAULT_ORDER_BY
    ): Paginator
    {
        // Create query base
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->from(static::ENTITY_NAME, static::ENTITY_SHORTCUT);
        $qb->select(static::ENTITY_SHORTCUT);

        // Filter all
        if (!empty($filters)) {
            $i = 0;
            foreach ($filters as $field => $value) {
                if ($i>0) {
                    $qb
                        ->andWhere(static::ENTITY_SHORTCUT . '.' . $field . ' = ?' . $i)
                        ->setParameter($i++, $value);
                } else {
                    $qb
                        ->where(static::ENTITY_SHORTCUT . '.' . $field . ' = ?' . $i)
                        ->setParameter($i++, $value);
                }
            }
        }

        // Order by ID
        $qb->orderBy(static::ENTITY_SHORTCUT . '.' . $orderBy[0], $orderBy[1]);

        // Limit for pagination
        $query = $qb->getQuery();
        $query->setMaxResults($max);
        $query->setFirstResult(--$page * $max);

        // Return paginator
        return new Paginator($query, false);
    }
}
