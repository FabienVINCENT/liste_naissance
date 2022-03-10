<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Tests\TestErrorsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRepositoryTest extends KernelTestCase
{
    use TestErrorsTrait;

    private ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    private function getEntity(): User
    {
        return (new User())
            ->setEmail('user@domain.com')
            ->setPassword('Passw@rd1234')
            ->setFirstname('Fabien')
            ->setLastname('Vincent');
    }

    public function testInvalidDuplicateEmail() {
        $user = $this->getEntity()->setEmail("user1@local.fr");
        $errors = $this->getErrors($user, 1);
        self::assertEquals('There is already an account with this email', $errors[0]->getMessage());
    }

    public function testValidHashPassword() {
        $entity = (new User())
            ->setEmail('user@domain.com');
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $entity->setPassword($passwordHasher->hashPassword($entity, 'P@ssword1234'));
        self::assertTrue($passwordHasher->isPasswordValid($entity, 'P@ssword1234'),);
    }

    public function testUnvalidHashPassword() {
        $entity = (new User())
            ->setEmail('user@domain.com');
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $entity->setPassword($passwordHasher->hashPassword($entity, 'P@ssword1234'));
        self::assertFalse($passwordHasher->isPasswordValid($entity, 'P@ssword1234d'),);
    }
}
