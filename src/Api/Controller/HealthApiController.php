<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contains the health check endpoint
 */
class HealthApiController extends AbstractController
{
    /**
     * A simple health check endpoint
     * 
     * @Route("/api/health")
     */
    public function check()
    {
        return new JsonResponse(['status' => 'ok'], Response::HTTP_OK);
    }
}
