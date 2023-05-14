<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\RequestInputParametersResolver;

return [
    RequestProviderInterface::class => RequestProvider::class,
    RequestInputParametersResolver::class => [
        '__construct()' => [
            'hydrator' => Reference::to(ValidatingHydrator::class),
        ],
    ],
];
