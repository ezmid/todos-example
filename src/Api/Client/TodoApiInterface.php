<?php declare(strict_types=1);

namespace App\Api\Client;

/**
 * Todo API interface
 */
interface TodoApiInterface extends ApiInterface
{
    /**
     * Find a todo
     */
    public function find(string $uuid): ?array;

    /**
     * Filter todos
     */
    public function list(array $options): array;

    /**
     * Create a todo
     */
    public function create(array $todo): ?array;

    /**
     * Update a todo
     */
    public function update(array $todo): ?array;

    /**
     * Mark a todo as done
     */
    public function markAsDone(string $uuid): bool;

    /**
     * Mark a todo as undone
     */
    public function markAsNew(string $uuid): bool;
}
