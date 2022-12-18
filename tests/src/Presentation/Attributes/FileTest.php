<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Presentation\Attributes;


use Ranky\SharedBundle\Infrastructure\Validator\RequestAttributeValidator;
use Ranky\SharedBundle\Presentation\Attributes\File\File;
use Ranky\SharedBundle\Presentation\Attributes\File\FileValueResolver;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Application\FileRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;

class FileTest extends BaseIntegrationTestCase
{

    public function testItShouldResolveControllerFileAttribute(): void
    {
        $path        = \sys_get_temp_dir().'/ranky_shared_bundle_test';
        $tmpFilename = \tempnam($path, 'FILE_RESOLVER');
        if (!$tmpFilename){
            throw new \RuntimeException(\error_get_last()['message'] ?? 'tempnam');
        }
        $handle = \fopen($tmpFilename, 'w');
        if (!$handle){
            throw new \RuntimeException(\error_get_last()['message'] ?? 'fopen');
        }
        \fwrite($handle, 'txt file');
        \fclose($handle);
        $uploaded = new UploadedFile(
            $tmpFilename,
            \basename($tmpFilename),
            'text/plain',
            null,
            true
        );

        $request = Request::create(uri: '/');
        $request->files->set('file', $uploaded);
        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithFileAttribute'];

        $dtoValidator      = $this->getService(RequestAttributeValidator::class);
        $fileValueResolver = new FileValueResolver($dtoValidator);
        $resolver          = new ArgumentResolver($factory, [$fileValueResolver]);
        $arguments         = $resolver->getArguments($request, $controller);

        /** @var FileRequest $fileRequest */
        $fileRequest = $arguments[0];
        @\unlink($tmpFilename);
        $this->assertInstanceOf(FileRequest::class, $fileRequest);
        $this->assertSame('text/plain', $fileRequest->mime());
    }

    public function controllerWithFileAttribute(#[File(constraint: null)] FileRequest $fileRequest): void
    {
    }

}
