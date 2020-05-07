<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Base frontend controller
 */
abstract class FrontendController extends AbstractController
{
    /**
     * @var string
     */
    const FLASH_MESSAGE_SUCCESS = 'success';

    /**
     * @var string
     */
    const FLASH_MESSAGE_ERROR = 'error';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Init dependencies
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Add a success flash message
     */
    protected function addFlashSuccess(string $message): void
    {
        /** @var Session */
        $session = $this->session;
        $session->getFlashBag()->add(static::FLASH_MESSAGE_SUCCESS, $message);
    }

    /**
     * Add an error flash message
     */
    protected function addFlashError(string $message): void
    {
        /** @var Session */
        $session = $this->session;
        $session->getFlashBag()->add(static::FLASH_MESSAGE_ERROR, $message);
    }

}
