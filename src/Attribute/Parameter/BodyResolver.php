<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Hydrator\Value;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

use function is_array;

final class BodyResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): Value
    {
        if (!$attribute instanceof Body) {
            throw new UnexpectedAttributeException(Body::class, $attribute);
        }

        $parsedBody = $this->requestProvider->get()->getParsedBody();

        $name = $attribute->getName() ?? $context->getParameter()->getName();

        if (!is_array($parsedBody)) {
            return Value::fail();
        }

        if (!ArrayHelper::pathExists($parsedBody, $name)) {
            return Value::fail();
        }

        return Value::success(ArrayHelper::getValueByPath($parsedBody, $name));
    }
}
