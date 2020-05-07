<?php

declare(strict_types=1);

namespace App\Api\Client\Internal;

use App\Api\Client\Internal\Response as ApiResponse;
use App\Api\Controller\ApiController;
use App\Api\Exception\ApiException;
use App\Api\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Internal API client implementation
 */
final class InternalApiClient
{
    /**
     * @var string
     */
    const PROFILER_GLUE = ':';

    /**
     * @var string
     */
    const PROFILER_PREFIX = 'INTERNAL';

    /**
     * @var string
     */
    const SERIALIZATION_FORMAT = 'json';

    /**
     * @var string
     */
    const SERIALIZATION_GROUPS = ['api'];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var string
     */
    private $lastErrorMessage = '';

    /**
     * Init dependencies
     */
    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        Stopwatch $stopwatch
    )
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->stopwatch = $stopwatch;
    }

    /**
     * Get the last recorded error message
     */
    public function getLastErrorMessage(): string
    {
        return $this->lastErrorMessage;
    }

    /**
     * Get a string representation for the profiler
     */
    private function getProfilerKey(ApiController $api, string $method): string
    {
        return implode(static::PROFILER_GLUE, [
            static::PROFILER_PREFIX,
            get_class($api),
            $method
        ]);
    }

    /**
     * Call the API in a controlled fashion internally as if it would be sent
     * over a network connection
     */
    public function call(
        ApiController $api,
        string $method,
        MessageInterface $message
    ): ResponseInterface
    {
        // Init profiling
        $stopWatchKey = $this->getProfilerKey($api, $method);
        $this->stopwatch->start($stopWatchKey);

        // Serialize the message
        $serialized = $this->serializer->serialize(
            $message,
            static::SERIALIZATION_FORMAT,
            [
                'groups' => static::SERIALIZATION_GROUPS
            ]
        );

        // Construct the request
        $request = new Request([], [], [], [], [], [], $serialized);
        $request->setMethod(Request::METHOD_POST);

        // Log attempt to file
        $this->logger->info(sprintf(
            'Internal API REQ: %s %s',
            get_class($api) . '->' . $method,
            $serialized
        ));

        // Call the API
        try {
            $response = $api->$method($request);
        } catch (ApiException $ex) {
            $this->lastErrorMessage = $ex->getMessage();

            // End stopwatch timer
            $this->stopwatch->stop($stopWatchKey);

            return (new ApiResponse())->setException($ex);
        }

        // Handle response cases
        if (!empty($response)) {
            // Log the internal response
            $content = $response->getContent();
            $this->logger->info(sprintf('Internal API RSP: %s', $content));

            // Deserialize
            $deserialized = json_decode($content, true);

            // Must not be an error
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $this->lastErrorMessage = $content;

                // @todo Refactor this to one standard way
                // Now we can receive 2 responses:
                // 1. {"status":"error", ... }
                // 2. {"error": { ... }, ... }
                if (isset($deserialized['error'])) {
                    $this->lastErrorMessage = $deserialized['error']['message'] ?: $deserialized['message'];

                    // End stopwatch timer
                    $this->stopwatch->stop($stopWatchKey);

                    // Hmm
                    return (new ApiResponse())
                        ->setException(new ApiException($this->lastErrorMessage));
                }

                // End stopwatch timer
                $this->stopwatch->stop($stopWatchKey);

                // Throw error
                throw new ApiException(sprintf(
                    'There was an error during the API call: %s',
                    $this->lastErrorMessage
                ));
            }

            // End stopwatch timer
            $this->stopwatch->stop($stopWatchKey);

            // Return Ok response
            return (new ApiResponse())->setContent($deserialized);
        }

        // End stopwatch timer
        $this->stopwatch->stop($stopWatchKey);

        // More verbose
        return (new ApiResponse())->setException(
            new ApiException('There was an error during the API call and the response is empty')
        );
    }
}
