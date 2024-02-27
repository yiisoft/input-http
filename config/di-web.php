<?php

declare(strict_types=1);

use Yiisoft\Definitions\Reference;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;

/** @var array $params */

return [
    RequestProviderInterface::class => [
        'class' => RequestProvider::class,
        'reset' => function () {
            /** @var RequestProvider $this */
            $this->request = null;
        },
    ],
    RequestInputParametersResolver::class => [
        '__construct()' => [
            'hydrator' => Reference::to(ValidatingHydrator::class),
            'throwInputValidationException' =>
                $params['yiisoft/input-http']['requestInputParametersResolver']['throwInputValidationException'],
        ],
    ],
];
