<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PageRequestConstraint
{
    /**
     * @return array<string, Constraint|array<Constraint>>
     */
    public function __invoke(): array
    {
        return [
            'id' => new NotBlank(),
            'title' => [new NotBlank(), new Length(max: 200)],
            'description' => [new NotBlank(), new Length(max: 255)],
        ];
    }

}
