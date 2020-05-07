<?php declare(strict_types=1);

namespace App\Api\Client\Internal;

use App\Api\Client\TodoApiInterface;
use App\Api\Message;
use App\Api\Controller\TodoApiController;

/**
 * Todo API client implementation
 */
class TodoApiClient implements TodoApiInterface
{
    /**
     * @var TodoApiController
     */
    private $api;

    /**
     * @var InternalApiClient
     */
    private $client;

    /**
     * Init dependencies
     */
    public function __construct(
        TodoApiController $api,
        InternalApiClient $client
    )
    {
        $this->api = $api;
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function call(
        string $method,
        Message\MessageInterface $message
    ): ResponseInterface
    {
        return $this->client->call(
            $this->api,
            $method,
            $message
        );
    }

    /**
     * @inheritDoc
     */
    public function getLastErrorMessage(): string
    {
        return $this->client->getLastErrorMessage();
    }

    /**
     * @inheritDoc
     */
    public function find(string $uuid): array
    {
        // Call API
        $response = $this->call(__FUNCTION__, (new Message\Identification())
            ->setUUID($uuid))
        ;

        // Check response
        if ($response->isOk() && !empty($response->getContent())) {
            return $response->getContent();
        }

        // We have nothing
        return [];
    }

    /**
     * @inheritDoc
     */
    public function create(array $todo): array
    {
        // Call api
        $response = $this->call(__FUNCTION__, (new Message\Todo\CreateMessage())
            ->setName($todo['name'])
            ->setDescription($todo['description'])
        );

        // Check response
        if ($response->isOk()) {
            $content = $response->getContent();
            if (isset($content['data'])) {
                return $content['data'];
            }
        }

        // We have nothing
        return [];
    }

    /**
     * @inheritDoc
     */
    public function update(array $todo): array
    {
        // Call api
        $response = $this->call(__FUNCTION__, (new Message\Todo\UpdateMessage())
            ->setUUID($todo['uuid'])
            ->setName($todo['name'])
            ->setDescription($todo['description'])
        );

        // Check response
        if ($response->isOk()) {
            $content = $response->getContent();
            if (isset($content['data'])) {
                return $content['data'];
            }
        }

        // We have nothing
        return [];
    }

    /**
     * @inheritDoc
     */
    public function markAsDone(string $uuid): bool
    {
        // Call api
        $response = $this->call(__FUNCTION__, (new Message\Identification())
            ->setUUID($uuid))
        ;

        // Check response
        return $response->isOk();
    }

    /**
     * @inheritDoc
     */
    public function markAsNew(string $uuid): bool
    {
        // Call api
        $response = $this->call(__FUNCTION__, (new Message\Identification())
            ->setUUID($uuid))
        ;

        // Check response
        return $response->isOk();
    }

    /**
     * @inheritDoc
     */
    public function list(array $options): array
    {
        // Call api
        $response = $this->call(
            __FUNCTION__,
            Message\ListRequest::factory($options)
        );

        // Check response
        if ($response->isOk()) {
            $content = $response->getContent();
            if (isset($content['data'])) {
                return $content['data'];
            }
        }

        // We have nothing
        return [];
    }
}
