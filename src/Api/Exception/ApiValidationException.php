<?php

declare(strict_types=1);

namespace App\Api\Exception;

/**
 * Common API validation exception definition
 */
class ApiValidationException extends ApiException
{
    /**
     * @var array
     */
    private $errors;

    /**
     * Init the parent Exception
     */
    public function __construct(array $errors) {
        // Format message for parrent
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error['property'] . ': ' . $error['message'];
        }

        parent::__construct(implode(';', $messages), 400);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
