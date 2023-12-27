<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Attributes\File;

use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ValidationFailedException;
use Ranky\SharedBundle\Infrastructure\Validator\RequestAttributeValidator;
use Ranky\SharedBundle\Presentation\Attributes\AttributeValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\InvalidMetadataException;


if (!\interface_exists('Symfony\Component\HttpKernel\Controller\ValueResolverInterface')) {
    interface MyValueResolverInterface extends \Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
    {
    }
} else {
    interface MyValueResolverInterface extends \Symfony\Component\HttpKernel\Controller\ValueResolverInterface
    {
    }
}

class FileValueResolver implements MyValueResolverInterface
{

    private RequestAttributeValidator $dtoValidatorResolver;

    public function __construct(RequestAttributeValidator $dtoValidatorResolver)
    {
        $this->dtoValidatorResolver = $dtoValidatorResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (!$argument->getAttributes() || null === $argument->getType()) {
            return false;
        }

        return $argument->getAttributes()[0] instanceof File
            && \is_a($argument->getType(), RequestDtoInterface::class, true);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<RequestDtoInterface|null>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return;
        }

        if (!$file = $request->files->get('file')) {
            yield null;

            return;
        }
        if (!$file instanceof UploadedFile) {
            yield null;

            return;
        }

        if (!$file->isValid()) {
            throw ValidationFailedException::fromThrowable(new \InvalidArgumentException($file->getErrorMessage()));
        }

        $data = [
            'path'      => $file->getRealPath(),
            'name'      => $file->getClientOriginalName(),
            'mime'      => $file->getMimeType() ?? $file->getClientMimeType(),
            'extension' => $file->guessExtension() ?? $file->getClientOriginalExtension(),
            'size'      => $file->getSize(),
        ];

        /* @var class-string<RequestDtoInterface> $type */
        if (!$type = $argument->getType()) {
            throw new InvalidMetadataException(
                'The argument type not found in the FileValueResolver class'
            );
        }
        /** @var array<File> $attributes */
        $attributes = $argument->getAttributes();

        $attributeValidator = new AttributeValidator(
            $type,
            $attributes[0]->getConstraint(),
            $attributes[0]->getGroups()
        );


        yield $this->dtoValidatorResolver->validate($attributeValidator, $data);
    }
}
