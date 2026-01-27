<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;
use Yiisoft\Input\Http\Attribute\Parameter\UploadedFilesResolver;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;

final class UploadedFilesTest extends TestCase
{
    public function testBase(): void
    {
        $file1 = $this->createMock(UploadedFileInterface::class);
        $file2 = $this->createMock(UploadedFileInterface::class);
        $file3 = $this->createMock(UploadedFileInterface::class);

        $hydrator = $this->createHydrator([
            'a' => $file1,
            'b' => $file2,
            'c' => $file3,
            'd' => [$file1, $file3],
        ]);

        $input = new class {
            #[UploadedFiles('a')]
            public ?UploadedFileInterface $a = null;
            #[UploadedFiles('b')]
            public ?UploadedFileInterface $b = null;
            #[UploadedFiles]
            public ?UploadedFileInterface $c = null;
            #[UploadedFiles('d')]
            public array $array = [];
        };

        $hydrator->hydrate($input);

        $this->assertSame($file1, $input->a);
        $this->assertSame($file2, $input->b);
        $this->assertSame($file3, $input->c);
        $this->assertSame([$file1, $file3], $input->array);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator([]);

        $input = new class {
            #[UploadedFiles('a')]
            public ?UploadedFileInterface $a = null;
            #[UploadedFiles('b')]
            public ?UploadedFileInterface $b = null;
            #[UploadedFiles]
            public ?UploadedFileInterface $c = null;
        };

        $hydrator->hydrate($input);

        $this->assertNull($input->a);
        $this->assertNull($input->b);
        $this->assertNull($input->c);
    }

    public function testNonExistPath(): void
    {
        $hydrator = $this->createHydrator([
            'a' => $this->createMock(UploadedFileInterface::class),
            'b' => $this->createMock(UploadedFileInterface::class),
        ]);

        $input = new class {
            #[UploadedFiles('a.b')]
            public ?UploadedFileInterface $a = null;
        };

        $hydrator->hydrate($input);

        $this->assertNull($input->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new UploadedFilesResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Body();
        $context = TestHelper::createParameterAttributeResolveContext();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . UploadedFiles::class . '", but "' . Body::class . '" given.');
        $resolver->getParameterValue($attribute, $context);
    }

    private function createHydrator(array $uploadedFiles): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUploadedFiles')->willReturn($uploadedFiles);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return TestHelper::createHydrator([
            UploadedFilesResolver::class => new UploadedFilesResolver($requestProvider),
        ]);
    }
}
