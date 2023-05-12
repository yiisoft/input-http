<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

final class RequestResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider
    )
    {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Request) {
            throw new UnexpectedAttributeException(Request::class, $attribute);
        }

        $requestAttributes = $this->requestProvider->get()->getAttributes();

        $name = $attribute->getName();
        if ($name === null) {
            return $requestAttributes;
        }

        if (!ArrayHelper::pathExists($requestAttributes, $name)) {
            throw new NotResolvedException();
        }

        return ArrayHelper::getValueByPath($requestAttributes, $name);
    }
}
