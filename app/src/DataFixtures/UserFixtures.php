<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@local.fr', $i));
            $password = $this->userPasswordHasher->hashPassword(
                $user,
                sprintf('P@ssword1234%d', $i)
            );
            $user->setPassword($password);
            $user->setFirstname('user');
            $user->setLastname('local');
            $manager->persist($user);
            $this->addReference(sprintf('user_%d', $i), $user);
        }

        $manager->flush();
    }
}
