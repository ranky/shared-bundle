<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\User\Domain;


interface UserRepository
{
    /**
     * @return array<int,User>
     */
    public function getAll(): array;
    public function getByUsername(string $username): User;
    public function getById(int $id): User;

    public function save(User $user): void;
    public function delete(User $user): void;
}
