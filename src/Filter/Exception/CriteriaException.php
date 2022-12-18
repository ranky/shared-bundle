<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\Exception;


use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

final class CriteriaException extends HttpDomainException
{

    public static function notValidField(string $field, string $criteriaClass): self
    {
        return new self(
            \sprintf(
                'The %s field not is valid field for the %s class',
                $field,
                $criteriaClass
            ),
            400
        );
    }
}
