<?php
declare(strict_types=1);

namespace Yiisoft\Input\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

interface RequestProviderInterface
{
    public function set(ServerRequestInterface $request): void;

    /**
     * @throws RequestNotSetException
     */
    public function get(): ServerRequestInterface;
}
