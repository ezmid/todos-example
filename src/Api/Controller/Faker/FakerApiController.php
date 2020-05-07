<?php

declare(strict_types=1);

namespace App\Api\Controller\Faker;

use App\Api\Controller\ApiController;
use Doctrine\ORM\EntityManagerInterface;
use Faker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The purpose of this class is to provide easy access to testing data
 */
abstract class FakerApiController extends ApiController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var Client
     */
    protected $ledger;

    /**
     * @todo For some reason the getError method gets called sooner than the constructor
     */
    public function __construct(
        EntityManagerInterface $em,
        NormalizerInterface $normalizer,
        SerializerInterface $serializer,
        TokenStorageInterface $ts,
        ValidatorInterface $validator
    )
    {
        parent::__construct($normalizer, $serializer);
        $this->em = $em;
        $this->ts = $ts;
        $this->validator = $validator;
    }
}
