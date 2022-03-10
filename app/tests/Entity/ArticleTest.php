<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Tests\TestErrorsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleTest extends KernelTestCase
{
    use TestErrorsTrait;

    private ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    private function getEntity(): Article
    {
        return (new Article())
            ->setTitle('Article super 1')
            ->setUrls(['https://google.fr', 'https://google.fr'])
            ->setPrice(10.99);
    }

    public function testValidEntity(): void
    {
        $this->getErrors($this->getEntity(), 0);
    }

    public function testTooShortTitle(): void
    {
        $this->getErrors($this->getEntity()->setTitle('aaaa'), 1);
    }

    public function testTooLongTitle(): void
    {
        $this->getErrors($this->getEntity()->setTitle(str_repeat('a', 256)), 1);
    }

    public function testPriceIsNeeded(): void
    {
        $this->getErrors(
            (new Article())
                ->setTitle('Article super 1')
                ->setUrls(['https://google.fr', 'https://google.fr']),
            1
        );
    }
}
