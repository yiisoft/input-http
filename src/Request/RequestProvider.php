<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

final class RequestProvider implements RequestProviderInterface
{
    private ?ServerRequestInterface $request = null;

    public function set(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function get(): ServerRequestInterface
    {
        if ($this->request === null) {
            throw new RequestNotSetException();
        }

        return $this->request;
    }
}
