<?php

declare(strict_types=1);

namespace App\Api\Controller\Faker;

use App\Entity\Todo;
use Faker;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The purpose of this class is to provide easy access to testing data
 */
final class TodoFakerApiController extends FakerApiController
{
    /**
     * @Route("/api/faker/todos/init")
     */
    public function init()
    {
        if ($this->getParameter('faker_enabled') !== 'yes') {
            return $this->getError([
                'message' => 'This route works only if you explicitly set the parameter "faker_enabled"="yes"'
            ]);
        }

        $generator = Faker\Factory::create();
        $populator = new Faker\ORM\Doctrine\Populator($generator, $this->em);
        
        $populator->AddEntity('\App\Entity\Todo', 50, [
            'status' => function() use ($generator) {
                return $generator->numberBetween(1, 2)*10;
            },
        ]);
        $populator->execute();

        return $this->getResponseStatusOK();
    }
}
