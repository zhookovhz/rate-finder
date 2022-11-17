<?php

declare(strict_types=1);

namespace Modules\Shared\Exceptions;

use Throwable;

abstract class DomainException extends \DomainException
{
    private const BASE_STATUS_CODE = 500;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if ($code === 0) {
            $code = self::BASE_STATUS_CODE;
        }

        parent::__construct($message, $code, $previous);
    }

    abstract public function getErrorAlias(): string;

    public function getPayload(): ?array
    {
        return null;
    }

    public function getDebugInfo(): ?array
    {
        return null;
    }
}
