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

final class FromBodyResolver implements DataAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider,
    )
    {
    }

    public function prepareData(DataAttributeInterface $attribute, Data $data): void
    {
        if (!$attribute instanceof FromBody) {
            throw new UnexpectedAttributeException(FromBody::class, $attribute);
        }

        $parsedBody = $this->requestProvider->get()->getParsedBody();
        if (is_array($parsedBody)) {
            $name = $attribute->getName();
            if ($name === null) {
                $data->setData([]);
            } else {
                $value = ArrayHelper::getValueByPath($parsedBody, $name);
                $data->setData(is_array($value) ? $value : []);
            }
        } else {
            $data->setData([]);
        }

        $data->setMap($attribute->getMap());
        $data->setStrict($attribute->isStrict());
    }
}
