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

use function is_array;

final class BodyResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private readonly RequestProviderInterface $requestProvider
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Body) {
            throw new UnexpectedAttributeException(Body::class, $attribute);
        }

        $parsedBody = $this->requestProvider->get()->getParsedBody();

        $name = $attribute->getName();
        if ($name === null) {
            return $parsedBody;
        }

        if (!is_array($parsedBody)) {
            throw new NotResolvedException();
        }

        if (!ArrayHelper::pathExists($parsedBody, $name)) {
            throw new NotResolvedException();
        }

        return ArrayHelper::getValueByPath($parsedBody, $name);
    }
}
