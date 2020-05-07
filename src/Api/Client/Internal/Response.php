<?php

declare(strict_types=1);

namespace App\Api\Client\Internal;

use App\Api\Exception\ApiException;

/**
 * Default internal API response
 */
class Response implements ResponseInterface
{
    /**
     * @var array
     */
    private $content;

    /**
     * @var ApiException
     */
    private $exception;

    /**
     * @inheritDoc
     * @todo WTF was I thinking.. :D
     */
    public function isOk(): bool
    {
        if (empty($this->exception) && !empty($this->content)) {
            if ((isset($this->content['status']) && $this->content['status'] == 'ok') || (is_array($this->content))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the response content
     */
    public function setContent($content): Response
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the response content
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * Set the API exception
     */
    public function setException(ApiException $exception): Response
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return !empty($this->exception) ? $this->exception->getMessage() : '';
    }
}
