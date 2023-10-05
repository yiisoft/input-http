<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Data;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeInterface;
use Yiisoft\Hydrator\Attribute\Data\DataAttributeResolverInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\DataInterface;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

use function is_array;

final class FromQueryResolver implements DataAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider,
    ) {
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromQuery) {
            throw new UnexpectedAttributeException(FromQuery::class, $attribute);
        }

        $array = [];

        $params = $this->requestProvider->get()->getQueryParams();
        $name = $attribute->getName();

        if ($name === null) {
            $array = $params;
        } else {
            $value = ArrayHelper::getValueByPath($params, $name);
            if (is_array($value)) {
                $array = $value;
            }
        }

        return new ArrayData($array, $attribute->getMap(), $attribute->isStrict());
    }
}
