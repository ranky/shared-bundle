<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Exception\ApiProblem;


use Symfony\Component\HttpFoundation\Response;

class ApiProblem implements \JsonSerializable
{

    /**
     * @var array<int, mixed>
     */
    private array $causes = [];
    public const DEFAULT_STATUS_CODE = 400; // 400 Bad Request

    /**
     * @param string $title
     * @param int $status
     * @param string|null $type
     * @param array<string, mixed> $details
     */
    public function __construct(
        private readonly string $title,
        private readonly int $status = self::DEFAULT_STATUS_CODE,
        private ?string $type = null,
        private array $details = []
    ) {
        if (!$this->type) {
            $this->type = Response::$statusTexts[$status];
        }
    }

    public function addCause(string $name, string $reason): void
    {
        $this->causes[] = ['name' => $name, 'reason' => $reason];
    }

    public function addDetail(string $key, mixed $value): void
    {
        $this->details[$key] = $value;
    }

    /**
     * @return array<int, mixed>
     */
    public function getCauses(): array
    {
        return $this->causes;
    }

    public function getStatus(): int
    {
        return $this->status;
    }


    public function getTitle(): string
    {
        return $this->title;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDetails(): array
    {
        return $this->details;
    }


    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'title' => $this->title,
            'type' => $this->type,
            'details' => $this->details,
            'causes' => $this->causes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
