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

/**
 * Resolver for {@see FromBody} attribute.
 */
final class FromBodyResolver implements DataAttributeResolverInterface
{
    /**
     * @param RequestProviderInterface $requestProvider The request provider.
     */
    public function __construct(
        private RequestProviderInterface $requestProvider,
    ) {
    }

    public function prepareData(DataAttributeInterface $attribute, DataInterface $data): DataInterface
    {
        if (!$attribute instanceof FromBody) {
            throw new UnexpectedAttributeException(FromBody::class, $attribute);
        }

        $array = [];

        $parsedBody = $this->requestProvider->get()->getParsedBody();
        if (is_array($parsedBody)) {
            $name = $attribute->getName();
            if ($name === null) {
                $array = $parsedBody;
            } else {
                $value = ArrayHelper::getValueByPath($parsedBody, $name);
                if (is_array($value)) {
                    $array = $value;
                }
            }
        }

        return new ArrayData($array, $attribute->getMap(), $attribute->isStrict());
    }
}
