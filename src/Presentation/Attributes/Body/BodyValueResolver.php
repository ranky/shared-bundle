<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Attributes\Body;

use Ranky\SharedBundle\Application\Dto\RequestDtoInterface;
use Ranky\SharedBundle\Presentation\Attributes\AttributeValidator;
use Ranky\SharedBundle\Infrastructure\Validator\RequestAttributeValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\InvalidMetadataException;


if (!\interface_exists('Symfony\Component\HttpKernel\Controller\ValueResolverInterface')) {
    interface MyValueResolverInterface extends \Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
    {
    }
}else{
    interface MyValueResolverInterface extends \Symfony\Component\HttpKernel\Controller\ValueResolverInterface
    {
    }
}
class BodyValueResolver implements MyValueResolverInterface
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

        return $argument->getAttributes()[0] instanceof Body
            && \is_a($argument->getType(), RequestDtoInterface::class, true);
    }


    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument
     * @throws \JsonException
     * @return iterable<RequestDtoInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        $data = \json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        /* @var class-string<RequestDtoInterface> $type */
        if (!$type = $argument->getType()) {
            throw new InvalidMetadataException(
                'The argument type not found in the BodyValueResolver class'
            );
        }

        /** @var array<Body> $attributes */
        $attributes = $argument->getAttributes();

        $attributeValidator = new AttributeValidator(
            $type,
            $attributes[0]->getConstraint(),
            $attributes[0]->getGroups()
        );

        yield $this->dtoValidatorResolver->validate($attributeValidator, $data);

        //yield $this->serializer->deserialize($requestFilterData, $argument->getType(), $request->getContentType());

    }
}
