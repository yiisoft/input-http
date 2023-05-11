<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request;

use LogicException;
use Throwable;

final class RequestNotSetException extends LogicException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Request is not set.', $code, $previous);
    }
}
