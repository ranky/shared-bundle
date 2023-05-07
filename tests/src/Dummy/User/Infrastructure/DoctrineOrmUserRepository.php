<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\User\Infrastructure;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ranky\SharedBundle\Tests\Dummy\User\Domain\UserRepository;
use Ranky\SharedBundle\Tests\Dummy\User\Domain\User;

/**
 * @extends ServiceEntityRepository<User>
 */
final class DoctrineOrmUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * @return array<int,User>
     */
    public function getAll(): array
    {
        /** @var array<int,User> $users */
        $users = $this->findAll();
        return $users;
    }

    public function getById(int $id): User
    {
        /** @var User|null $user */
        $user = $this->find($id);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }
        return $user;
    }

    public function getByUsername(string $username): User
    {
        /** @var User|null $user */
        $user =  $this->findOneBy(['username' => $username]);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }
        return $user;
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
