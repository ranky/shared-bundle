<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Exception\ApiProblem;


use Ranky\SharedBundle\Domain\Exception\HttpDomainException;

class ApiProblemException extends HttpDomainException
{

    public function __construct(private readonly ApiProblem $apiProblem, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $apiProblem->getTitle(),
            $apiProblem->getStatus(),
            $code,
            $previous
        );
        if ($previous) {
            $this->file = $previous->getFile();
            $this->line = $previous->getLine();
        }
    }

    public function getApiProblem(): ApiProblem
    {
        return $this->apiProblem;
    }

    public static function create(string $title, int $status = ApiProblem::DEFAULT_STATUS_CODE): self
    {
        $apiProblem = new ApiProblem($title, $status);

        return new self($apiProblem);
    }

    public static function fromThrowable(\Throwable $throw): self
    {
        $statusCode = \method_exists($throw, 'getStatusCode')
            ? $throw->getStatusCode()
            : ApiProblem::DEFAULT_STATUS_CODE;

        $apiProblem = \method_exists($throw, 'getApiProblem')
            ? $throw->getApiProblem()
            : new ApiProblem($throw->getMessage(), $statusCode);

        return new self($apiProblem, 0, $throw);
    }


}
