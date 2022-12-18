<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Exception\ApiProblem;


use Symfony\Component\HttpFoundation\Response;

final class CouldNotFindRequestValueException extends ApiProblemException
{
    public function __construct(string $field, int $code = 0, ?\Throwable $previous = null)
    {
        $message    = \sprintf('Could not find %s value on request object', $field);
        $apiProblem = new ApiProblem($message, Response::HTTP_BAD_REQUEST);

        parent::__construct(
            $apiProblem,
            $code,
            $previous
        );
    }

}
