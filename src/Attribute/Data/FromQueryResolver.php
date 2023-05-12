<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Data;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\DataAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

use function is_array;

final class FromQueryResolver implements DataAttributeResolverInterface
{
    public function __construct(
        private readonly RequestProviderInterface $requestProvider,
    ) {
    }

    public function prepareData(DataAttributeInterface $attribute, Data $data): void
    {
        if (!$attribute instanceof FromQuery) {
            throw new UnexpectedAttributeException(FromQuery::class, $attribute);
        }

        $params = $this->requestProvider->get()->getQueryParams();
        $name = $attribute->getName();

        if ($name === null) {
            $data->setData($params);
        } else {
            $value = ArrayHelper::getValueByPath($params, $name);
            $data->setData(is_array($value) ? $value : []);
        }

        $data->setMap($attribute->getMap());
        $data->setStrict($attribute->isStrict());
    }
}
