<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * RequestProviderInterface provides a way to set the current request and then get it in attribute handlers.
 */
interface RequestProviderInterface
{
    /**
     * Set the current request.
     *
     * @param ServerRequestInterface $request The request to set.
     */
    public function set(ServerRequestInterface $request): void;

    /**
     * Get the current request.
     *
     * @throws RequestNotSetException
     */
    public function get(): ServerRequestInterface;
}
