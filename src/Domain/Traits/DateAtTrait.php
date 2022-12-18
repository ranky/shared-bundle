<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Domain\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait DateAtTrait
{

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private \DateTimeImmutable $createdAt;


    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;


    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
