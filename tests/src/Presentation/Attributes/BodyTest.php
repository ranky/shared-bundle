<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Presentation\Attributes;


use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Infrastructure\Validator\RequestAttributeValidator;
use Ranky\SharedBundle\Presentation\Attributes\Body\Body;
use Ranky\SharedBundle\Presentation\Attributes\Body\BodyValueResolver;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Application\PageRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;

class BodyTest extends BaseIntegrationTestCase
{


    /**
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function testItShouldResolveControllerBodyAttribute(): void
    {
        $page        = PageFactory::random()[0];
        $pageRequest = new PageRequest($page->getId(), $page->getTitle(), $page->getDescription());
        $data        = ClassHelper::objectToArray($pageRequest);

        $request    = Request::create(uri: '/', content: \json_encode($data, JSON_THROW_ON_ERROR));
        $factory    = new ArgumentMetadataFactory();
        $controller = [new self(), 'controllerWithBodyAttribute'];
        //$argumentsMetadata = $factory->createArgumentMetadata($controller);

        $dtoValidator      = $this->getService(RequestAttributeValidator::class);
        $bodyValueResolver = new BodyValueResolver($dtoValidator);
        $resolver          = new ArgumentResolver($factory, [$bodyValueResolver]);

        $arguments = $resolver->getArguments($request, $controller);
        $this->assertContainsEquals($pageRequest, $arguments);
    }

    public function controllerWithBodyAttribute(#[Body(constraint: null)] PageRequest $pageRequest): void
    {
    }

}
