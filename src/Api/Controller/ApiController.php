<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Exception\ApiException;
use App\Api\Exception\ApiValidationException;
use App\Api\Message\Identification;
use App\Api\Message\ListRequest;
use App\Api\Message\MessageInterface;
use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Basic parent controller for all API services
 */
class ApiController extends AbstractController
{ 
    /**
     * @var string
     */
    const DEFAULT_ENTITY = Todo::class;

    /**
     * @var string
     */
    const OK = 'ok';

    /**
     * @var int
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * Enforce JSON output, maybe we should consider using a header
     */
    const ENFORCED_FORMAT = 'json';

    /**
     * Default serialization annotation
     */
    const DEFAULT_SERIALIZATION_ANNOTATION = 'api';

    /**
     * Default serialization/normalization groups
     */
    const DEFAULT_GROUPS = [
        self::DEFAULT_SERIALIZATION_ANNOTATION
    ];

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Init dependencies
     */
    public function __construct(
        NormalizerInterface $normalizer,
        SerializerInterface $serializer
    )
    {
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
    }

    /**
     * Will return a serialized API response View
     */
    protected function getResponse(
        $data,
        int $code = Response::HTTP_OK
    ): Response
    {
        /** @var NormalizerInterface */
        $serializer = $this->serializer;
        $normalized = $serializer->normalize(
            $data,
            null,
            ['groups' => static::DEFAULT_GROUPS]
        );
        
        return new JsonResponse($normalized, $code);
    }

    /**
     * Return a simple OK message
     */
    protected function getResponseStatusOK($data = null): Response
    {
        return !empty($data) ? $this->getResponse([
            'status' => static::OK,
            'data' => $data
        ]) : $this->getResponse([
            'status' => static::OK
        ]);
    }

    /**
     * Return a serialized API error View
     */
    protected function getError(
        array $data,
        int $code = Response::HTTP_BAD_REQUEST,
        $groups = self::DEFAULT_GROUPS
    ): Response
    {
        $normalized = $this->normalizer->normalize($data, null, $groups);
        return new JsonResponse($normalized, $code);
    }

    /**
     * Takes only the usefull information out of the validation proces
     */
    protected function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            $result[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
                'invalid' => $error->getInvalidValue()
            ];
        }

        return $result;
    }

    /**
     * Decode the incoming message
     */
    protected function decodeMessage(
        Request $request,
        string $type = Identification::class
    ): ?MessageInterface
    {
        // Try to find an Identification message
        if ($request->isMethod('post')) {
            $content = $request->getContent();
            if (!empty($content)) {
                /** @var MessageInterface*/
                $message = $this->lastMessage = $this->serializer->deserialize(
                    $content,
                    $type,
                    static::ENFORCED_FORMAT
                );

                // Check the message
                $errors = $this->validator->validate($message);
                if (count($errors) !== 0) {
                    throw new ApiValidationException($this->formatValidationErrors($errors));
                }

                return $message;
            }
        }

        return null;
    }

    /**
     * Decode the list request
     */
    protected function decodeListRequest(Request $request): ?ListRequest
    {
        /** @var ListRequest */
        $decoded = $this->decodeMessage($request, ListRequest::class);
        return $decoded;
    }

    /**
     * Find an entity by an identification message from the request or ID
     */
    protected function findOne(
        ?Request $request,
        ?string $uuid = null,
        string $entityType = self::DEFAULT_ENTITY,
        string $messageType = Identification::class
    )
    {
        // Try to find by ID, then decode from request
        if (!empty($uuid)) {
            $entity = $this->getDoctrine()
                ->getManager()
                ->getRepository($entityType)
                ->find($uuid)
            ;
        } elseif (!empty($request)) {
            // Try to decode the message from the request parameter
            /** @var Identification */
            $message = $this->decodeMessage($request, $messageType);
            if (!empty($message) && !$message->isEmpty()) {
                $entity =$this->getDoctrine()
                    ->getManager()
                    ->getRepository($entityType)
                    ->findOneBy($message->getFindByOptions());
            }
        }

        // We did not found the entity
        if (empty($entity)) {
            $parts = explode('\\', $entityType);
            $name = array_pop($parts);
            $uuid = !empty($uuid) ? $uuid : (isset($message) ? $message->getUUID() : 0);
            throw new ApiException(sprintf(
                '%s with id=%s was not found.',
                $name,
                $uuid
            ));
        }

        return $entity;
    }
}
