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

final class QueryResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider
    ) {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Query) {
            throw new UnexpectedAttributeException(Query::class, $attribute);
        }

        $params = $this->requestProvider->get()->getQueryParams();

        $name = $attribute->getName();
        if ($name === null) {
            return $params;
        }

        if (!ArrayHelper::pathExists($params, $name)) {
            throw new NotResolvedException();
        }

        return ArrayHelper::getValueByPath($params, $name);
    }
}
