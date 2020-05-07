<?php

declare(strict_types=1);

namespace App\Api\Controller\Faker;

use App\Api\Controller\ApiController;
use App\Api\Exception\ApiException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * The purpose of this class is to provide easy access to testing data
 */
final class DatabaseFakerApiController extends ApiController
{
    /**
     * @Route("/api/faker/database")
     */
    public function recreate(KernelInterface $kernel)
    {
        if ($this->getParameter('faker_enabled') === 'yes') {
            $content = [];
            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Create DB
            $input = new ArrayInput([
               'command' => 'doctrine:database:create',
               '--env' => 'prod',
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            // Drop
            $input = new ArrayInput([
               'command' => 'doctrine:schema:drop',
               '--env' => 'prod',
               '--force' => true,
               '--full-database' => true,
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            // Create
            $input = new ArrayInput([
               'command' => 'doctrine:schema:update',
               '--env' => 'prod',
               '--force' => true,
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            return $this->getResponse([
                'output' => $content
            ]);
        }

        throw new ApiException('This route works only if you explicitly set the parameter "faker_enabled"="yes"');
    }

    /**
     * @Route("/api/faker/database/schema/update")
     */
    public function schemaUpdate(KernelInterface $kernel)
    {
        if ($this->getParameter('faker_enabled') === 'yes') {
            $content = [];
            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Create
            $input = new ArrayInput([
               'command' => 'doctrine:schema:update',
               '--env' => 'prod',
               '--force' => true,
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            return $this->getResponse([
                'output' => $content
            ]);
        }

        throw new ApiException('This route works only if you explicitly set the parameter "faker_enabled"="yes"');
    }

    /**
     * @Route("/api/faker/database/schema/drop")
     */
    public function schemaDrop(KernelInterface $kernel)
    {
        if ($this->getParameter('faker_enabled') === 'yes') {
            $content = [];
            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Create
            $input = new ArrayInput([
               'command' => 'doctrine:schema:drop',
               '--env' => 'prod',
               '--force' => true,
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            return $this->getResponse([
                'output' => $content
            ]);
        }

        throw new ApiException('This route works only if you explicitly set the parameter "faker_enabled"="yes"');
    }

    /**
     * @Route("/api/faker/database/migrate")
     */
    public function databaseMigrate(KernelInterface $kernel)
    {
        if ($this->getParameter('faker_enabled') === 'yes') {
            $content = [];
            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Create
            $input = new ArrayInput([
               'command' => 'doctrine:migrations:migrate',
               '--env' => 'prod',
               '--no-interaction' => true,
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            return $this->getResponse([
                'output' => $content
            ]);
        }

        throw new ApiException('This route works only if you explicitly set the parameter "faker_enabled"="yes"');
    }

    /**
     * @Route("/api/faker/doctrine/cache/clear")
     */
    public function doctrineCacheClear(KernelInterface $kernel)
    {
        if ($this->getParameter('faker_enabled') === 'yes') {
            $content = [];
            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Create
            $input = new ArrayInput([
               'command' => 'doctrine:cache:clear-metadata',
               '--env' => 'prod',
               '--no-ansi' => true,
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            $content[] = $output->fetch();

            return $this->getResponse([
                'output' => $content
            ]);
        }

        throw new ApiException('This route works only if you explicitly set the parameter "faker_enabled"="yes"');
    }
}


