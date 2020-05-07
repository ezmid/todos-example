<?php declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Exception\ApiException;
use App\Api\Message\ListRequest;
use App\Api\Message\ListResponse;
use App\Api\Message\Todo\CreateMessage;
use App\Api\Message\Todo\UpdateMessage;
use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Todo related API methods
 */
final class TodoApiController extends ApiController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Init dependencies
     */
    public function __construct(
        NormalizerInterface $normalizer,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {
        parent::__construct($normalizer, $serializer);
        $this->validator = $validator;
    }

    /**
     * Simple get
     *
     * @Route(
     *      "/api/todo/find/{uuid}",
     * )
     * @Route("/api/todo/find", methods={"POST"})
     */
    public function find(Request $request, ?string $uuid = null)
    {
        return $this->getResponse(
            $this->findOne($request, $uuid, Todo::class)
        );
    }

    /**
     * List todos
     *
     * @Route("/api/todo", methods={"GET","HEAD","POST"})
     */
    public function list(Request $request)
    {
        // Decode message
        $msg = $this->decodeListRequest($request);

        // Init a default object in case there is no filter request
        if (empty($msg)) {
            $msg = (new ListRequest())
                ->setPage(intval($request->get('page', 1)))
                ->setLimit(intval($request->get(
                    'limit',
                    TodoRepository::DEFAULT_QUERY_LIMIT
                )))
            ;
        }

        // Route the repository
        /** @var TodoRepository */
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Todo::class);

        // Filter todos
        $todos = $repository->filterByListRequest($msg);

        // Pages
        $pages = ceil($todos->count() / $todos->getQuery()->getMaxResults());

        // Create list response
        $list = (new ListResponse())
            ->setPage($msg->getPage())
            ->setLimit($msg->getLimit())
            ->setOrderBy($msg->getOrderBy())
            ->setFilter($msg->getFilter())
            ->setNumOfPages(intval($pages))
            ->setCount($todos->count())
            ->setItems($todos)
        ;

        // Respond
        return $this->getResponseStatusOK($list);
    }

    /**
     * Create a todo
     *
     * @Route("/api/todo", methods={"PUT"})
     */
    public function create(Request $request)
    {
        // Decode the update message
        /** @var CreateMessage */
        $msg = $this->decodeMessage($request, CreateMessage::class);

        // Create the entity
        $todo = (new Todo())
            ->setName($msg->getName())
            ->setDescription($msg->getDescription())
        ;

        // Validate the newly created entity
        $errors = $this->validator->validate($todo);
        if (count($errors) === 0) {
            try {
                // Commit changes
                $em = $this->getDoctrine()->getManager();
                $em->persist($todo);
                $em->flush();

                // Respond
                return $this->getResponseStatusOK($todo);
            } catch (UniqueConstraintViolationException $ex) {
                throw new ApiException('The code must be unique across all todos');
            }
        }

        // Respond with the error
        return $this->getError([
            'status' => 'error',
            'message' => 'Todo is not valid',
            'data' => $errors
        ]);
    }

    /**
     * Update a todo
     *
     * @Route("/api/todo/update")
     */
    public function update(Request $request)
    {
        // Decode the update message
        /** @var UpdateMessage */
        $msg = $this->decodeMessage($request, UpdateMessage::class);

        // Find and update the entity
        /** @var Todo */
        $todo = $this->getDoctrine()
            ->getManager()
            ->getRepository(Todo::class)
            ->findOneBy(['uuid' => $msg->getUUID()])
        ;

        // Find the entity
        if (empty($todo)) {
            throw new ApiException(sprintf(
                'Todo #%s not found',
                $msg->getUUID()
            ));
        }

        // Update entity
        $todo
            ->setName($msg->getName())
            ->setDescription($msg->getDescription())
        ;

        // Validate entity
        $errors = $this->validator->validate($todo);
        if (count($errors) === 0) {
            // Commit changes
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            // All is fine
            return $this->getResponseStatusOK($todo);
        }

        // Return error info
        return $this->getError([
            'message' => 'Validation failed',
            'errors' => $this->formatValidationErrors($errors)
        ]);
    }

    /**
     * Mark the todo as done
     *
     * @Route(
     *      "/api/todo/mark-as-done/{uuid}",
     * )
     * @Route("/api/todo/mark-as-done")
     */
    public function markAsDone(Request $request, ?string $uuid = null)
    {
        // Find
        $todo = $this->findOne($request, $uuid, Todo::class);
        $before = [
            'status' => $todo->getStatus(),
            'label' => $todo->getStatusLabel()
        ];

        // Update
        $todo->setStatus(Todo::STATUS_DONE);

        // Commit changes
        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();

        // Respond
        return $this->getResponseStatusOK([
            'before' => $before,
            'current' => [
                'status' => $todo->getStatus(),
                'label' => $todo->getStatusLabel()
            ]
        ]);
    }

    /**
     * Mark the todo as new
     *
     * @Route(
     *      "/api/todo/mark-as-new/{uuid}",
     * )
     * @Route("/api/todo/mark-as-new")
     */
    public function markAsNew(Request $request, ?string $uuid = null)
    {
        // Find
        $todo = $this->findOne($request, $uuid, Todo::class);
        $before = [
            'status' => $todo->getStatus(),
            'label' => $todo->getStatusLabel()
        ];

        // Update
        $todo->setStatus(Todo::STATUS_NEW);

        // Commit changes
        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();

        // Respond
        return $this->getResponseStatusOK([
            'before' => $before,
            'current' => [
                'status' => $todo->getStatus(),
                'label' => $todo->getStatusLabel()
            ]
        ]);
    }
}
