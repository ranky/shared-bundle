<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Infrastructure\Validator;

use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Common\ClassHelper;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ValidationFailedException;
use Ranky\SharedBundle\Infrastructure\Validator\RequestAttributeValidator;
use Ranky\SharedBundle\Presentation\Attributes\AttributeValidator;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Application\PageRequest;
use Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Validator\PageRequestConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestAttributeValidatorTest extends BaseIntegrationTestCase
{
    private RequestAttributeValidator $requestAttributeValidator;
    private AttributeValidator $attributeValidator;

    protected function setUp(): void
    {
        $validator                       = $this->getService(ValidatorInterface::class);
        $this->requestAttributeValidator = new RequestAttributeValidator($validator);
        $this->attributeValidator        = new AttributeValidator(PageRequest::class, PageRequestConstraint::class);
    }


    /**
     * @throws \ReflectionException
     */
    public function testIShouldCreateAndValidateControllerAttribute(): void
    {
        $page                = PageFactory::random()[0];
        $pageRequest         = new PageRequest($page->getId(), $page->getTitle(), $page->getDescription());
        $data                = ClassHelper::objectToArray($pageRequest);
        $expectedPageRequest = $this->requestAttributeValidator->validate(
            $this->attributeValidator,
            $data
        );

        $this->assertEquals(
            $pageRequest,
            $expectedPageRequest
        );
    }

    public function testIShouldCreateAndValidateControllerAttributeWithViolations(): void
    {
        $page          = PageFactory::random()[0];
        $pageRequest   = new PageRequest($page->getId(), $page->getTitle(), $page->getDescription());
        $data          = ClassHelper::objectToArray($pageRequest);
        $data['title'] .= ' '.$data['title'];
        $data['title'] .= ' '.$data['description'];

        $this->expectException(ValidationFailedException::class);
        $this->requestAttributeValidator->validate($this->attributeValidator, $data);
    }
}
