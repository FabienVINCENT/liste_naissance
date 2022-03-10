<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\TestErrorsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    use TestErrorsTrait;

    const BAD_EMAIL = 'user@domain';
    const ERROR_MESSAGE_EMAIL = 'L\'email "'.self::BAD_EMAIL.'" n\'est pas valide.';
    const ERROR_MESSAGE_PASSWORD_LONG = 'Le mot de passe doit contenir au maximum ' . User::PASSWORD_MAX_LENGTH . ' caractères.';
    const ERROR_MESSAGE_FIRSTNAME_LONG = 'Votre prénom ne doit pas dépasser ' . User::FIRSTNAME_LENGTH . ' caractères.';
    const ERROR_MESSAGE_LASTNAME_LONG = 'Votre nom ne doit pas dépasser ' . User::LASTNAME_LENGTH . ' caractères.';
    const ERROR_MESSAGE_BLANK = 'Cette valeur ne doit pas être vide.';

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

    public function testValidEntity() {
        $this->getErrors($this->getEntity(), 0);
    }

    public function testInvalidEmail() {
        $errors = $this->getErrors($this->getEntity()->setEmail(self::BAD_EMAIL), 1);
        self::assertEquals(self::ERROR_MESSAGE_EMAIL, $errors[0]->getMessage());
    }

    public function testInvalidPasswordToShort() {
        $this->getErrors($this->getEntity()->setPassword('Passw@r'), 1);
    }

    public function testInvalidPasswordToLong() {
        $errors = $this->getErrors($this->getEntity()->setPassword('Passw@rd1234'.str_repeat('a', 255-12+1)), 1);
        self::assertEquals(self::ERROR_MESSAGE_PASSWORD_LONG, $errors[0]->getMessage());
    }

    public function testInvalidFirstnameToLong() {
        $errors = $this->getErrors($this->getEntity()->setFirstname(str_repeat('a', 101)), 1);
        self::assertEquals(self::ERROR_MESSAGE_FIRSTNAME_LONG, $errors[0]->getMessage());
    }

    public function testInvalidFirstnameBlank() {
        $errors = $this->getErrors($this->getEntity()->setFirstname(''), 1);
        self::assertEquals(self::ERROR_MESSAGE_BLANK, $errors[0]->getMessage());
    }

    public function testInvalidLastnameToLong() {
        $errors = $this->getErrors($this->getEntity()->setLastname(str_repeat('a', 101)), 1);
        self::assertEquals(self::ERROR_MESSAGE_LASTNAME_LONG, $errors[0]->getMessage());
    }

    public function testInvalidLastnameBlank() {
        $errors = $this->getErrors($this->getEntity()->setLastname(''), 1);
        self::assertEquals(self::ERROR_MESSAGE_BLANK, $errors[0]->getMessage());
    }
}
