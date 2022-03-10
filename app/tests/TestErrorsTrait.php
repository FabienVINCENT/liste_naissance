<?php

namespace App\Tests;

use Symfony\Component\Validator\ConstraintViolationList;

trait TestErrorsTrait
{
    private function getErrors(object $user, int $expectedCountErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);
        $messages = [];
        foreach($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        self::assertCount($expectedCountErrors, $errors, implode(', ', $messages));
        return $errors;
    }
}
