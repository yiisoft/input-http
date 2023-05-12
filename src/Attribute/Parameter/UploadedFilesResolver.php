<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributeInterface;
use Yiisoft\Hydrator\ParameterAttributeResolverInterface;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

final class UploadedFilesResolver implements ParameterAttributeResolverInterface
{
    public function __construct(
        private RequestProviderInterface $requestProvider
    ) {
    }

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        Context $context
    ): array|UploadedFileInterface {
        if (!$attribute instanceof UploadedFiles) {
            throw new UnexpectedAttributeException(UploadedFiles::class, $attribute);
        }

        $files = $this->requestProvider->get()->getUploadedFiles();

        $name = $attribute->getName();
        if ($name === null) {
            return $files;
        }

        if (!ArrayHelper::pathExists($files, $name)) {
            throw new NotResolvedException();
        }

        /** @var UploadedFileInterface */
        return ArrayHelper::getValueByPath($files, $name);
    }
}
