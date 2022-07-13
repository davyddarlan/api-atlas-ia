<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidatorManager
{
    private $validatorInterface;
    private $constraints = [];
    
    public function __construct(ValidatorInterface $validatorInterface) 
    {
        $this->validatorInterface = $validatorInterface;
    }

    public function validate($entity): self
    {
        $errors = $this->validatorInterface->validate($entity);

        if ($errors) {
            foreach ($errors->getIterator() as $constrait) {
                if (array_key_exists($constrait->getPropertyPath(), $this->constraints)) {
                    array_push($this->constraints[$constrait->getPropertyPath()], $constrait->getMessage());
                } else {
                    $this->constraints[$constrait->getPropertyPath()] = [$constrait->getMessage()];
                }
            }
        }

        return $this;
    }

    public function hasError(): bool
    {
        return count($this->constraints) ? true : false;
    }

    public function response(): Response
    {
        return new JsonResponse($this->constraints, 422);
    }
}