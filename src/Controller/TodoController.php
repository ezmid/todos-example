<?php

declare(strict_types=1);

namespace App\Controller;

use App\Api\Client\Internal\TodoApiClient;
use App\Controller\FrontendController;
use App\Entity\Todo;
use App\Form\Type\TodoFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * All todo related actions
 */
final class TodoController extends FrontendController
{
    /**
     * @var TodoApiClient
     */
    private $api;

    /**
     * Init dependencies
     */
    public function __construct(
        TodoApiClient $api,
        SessionInterface $session
    )
    {
        parent::__construct($session);
        $this->api = $api;
    }

    /**
     * Homepage
     *
     * @Route(
     *      "/",
     *      name="homepage"
     * )
     */
    public function homepage(Request $request)
    {
        return $this->list($request);
    }

    /**
     * Display a list of available todos
     *
     * @Route(
     *      "/todo/list",
     *      name="todo_list"
     * )
     */
    public function list(Request $request)
    {
        // Filter by state if desired
        $literal = $request->get('filter', 'all');

        // List
        $list = $this->api->list([
            'filter' => [
                // 'literal' => $literal,
            ],
            'page' => intval($request->get('page', 1)),
            'limit' => 10,
            'orderBy' => ['id', 'DESC']
        ]);

        // Render view
        return $this->render('todos/list.html.twig', [
            'filter' => $literal,
            'list' => $list,
        ]);
    }

    /**
     * View information about a todo
     *
     * @Route(
     *      "/todo/view/{uuid}",
     *      name="todo_view",
     *      requirements={
     *          "uuid"="[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}"
     *      }
     * )
     */
    public function viewAndEdit(Request $request, string $uuid)
    {
        // Find the todo item by uuid
        $todo = $this->api->find($uuid);

        // Create update form
        $form = $this->createForm(TodoFormType::class, $todo);

        // Handle submit
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Call API
                $response = $this->api->update(array_merge(
                    $request->request->all(),
                    ['uuid' => $uuid]
                ));

                // Check result
                if ($response) {
                    // Add notification and redirect
                    $this->addFlashSuccess('Todo was updated');
                    return $this->redirectToRoute('todo_view', ['uuid' => $response['uuid']]);
                }

                // Notify
                $this->addFlashError('Form is not valid');
            } else {
                $this->addFlashError('Form is not valid');
            }
        }

        return $this->render('todos/view.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Add a todo
     *
     * @Route(
     *      "/todo/add",
     *      name="todo_add"
     * )
     */
    public function add(Request $request)
    {
        $todo = (new Todo())
            ->setUUID('')
            ->setName('My new task')
            ->setDescription('')
        ;

        // Create a new todo with a new form
        $form = $this->createForm(TodoFormType::class, $todo);

        // Handle submit
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Call API
                $response = $this->api->create($request->request->all());

                // Check result
                if ($response) {
                    // Add notification and redirect
                    $this->addFlashSuccess('A new todo was created');
                    return $this->redirectToRoute('todo_view', ['uuid' => $response['uuid']]);
                }

                // Notify
                $this->addFlashError('Form is not valid');
            } else {
                $this->addFlashError('Form is not valid');
            }
        }

        return $this->render('todos/add.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Discontinue a todo
     *
     * @Route(
     *      "/todo/{uuid}/mark-as-done",
     *      name="todo_mark_as_done",
     * )
     */
    public function markAsDone(Request $request, string $uuid)
    {
        // Call the API
        if ($this->api->markAsDone($uuid)) {
            $this->addFlashSuccess(sprintf(
                'Todo #%s was marked as done',
                $uuid
            ));
        } else {
            $this->addFlashError($this->api->getLastErrorMessage());
        }

        // Custom redirect
        $redirect = $request->get('redirect');
        if (!empty($redirect)) {
            return $this->redirect($redirect);
        }

        return $this->redirectToRoute('todo_view', ['uuid' => $uuid]);
    }

    /**
     * Mark a todo as new
     *
     * @Route(
     *      "/todo/{uuid}/mark-as-new",
     *      name="todo_mark_as_new",
     * )
     */
    public function markAsNew(Request $request, string $uuid)
    {
        // Call the API
        if ($this->api->markAsNew($uuid)) {
            $this->addFlashSuccess(sprintf(
                'Todo #%s was marked as new',
                $uuid
            ));
        } else {
            $this->addFlashError($this->api->getLastErrorMessage());
        }

        // Custom redirect
        $redirect = $request->get('redirect');
        if (!empty($redirect)) {
            return $this->redirect($redirect);
        }

        return $this->redirectToRoute('todo_view', ['uuid' => $uuid]);
    }
}
