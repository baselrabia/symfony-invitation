<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class BaseRequest
{
    protected $validator;
    protected $request;
    private $fields;
    /**
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    public $headers;


    public function __construct(ValidatorInterface $validator, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();

        $this->fields = $this->rules();
        $this->validator = $validator;
        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
        $this->headers = $this->request->headers;
    }

    public function validate()
    {
        $errors = [];
        $messages = ['message' => 'validation_failed', 'errors' => []];

        foreach ($this->all() as $field => $value){
            $errors[$field] = $this->validator->validate($value, $this->getConstraints()[$field]);

            /** @var \Symfony\Component\Validator\ConstraintViolation  */
            foreach ($errors[$field] as $message) {
                 $messages['errors'][] = [
                    'property' => $field,
                    'value' => $message->getInvalidValue(),
                    'message' => $message->getMessage(),
                ];
            }
        }

        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, 201);
            $response->send();
            exit;
        }
    }
    public function mapAsserts(): array
    {
        return [
            "Required" => new Assert\NotNull(),
            "NotBlank" => new Assert\NotBlank(),
            "Integer" =>  new Assert\Type('integer'),
            "String" =>  new Assert\Type('string'),
            "Email" => new Assert\Email([ 'message' => 'The email "{{ value }}" is not a valid email.',]),
        ];
    }


    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    protected function populate(): void
    {

        foreach (array_keys($this->fields) as $property) {
            $this->{$property} = "";
        }

        foreach ($this->request->request->all() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    public function all(): array
    {
        $result = [];
        foreach (array_keys($this->fields) as $property) {
            $result[$property] =  $this->{$property};
        }
        return $result;
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }

    private function getConstraints(): array
    {
        $constrains = [];
        foreach ($this->fields as $parameter => $rules){
            $constrains[$parameter] =  $this->getRuleConstrain($rules);
        }
        return $constrains;
    }

    private function getRuleConstrain(array $rules): array
    {
        $asserts = [];
        foreach ($rules as $rule){
            if(isset($this->mapAsserts()[$rule])){
                $asserts[] = $this->mapAsserts()[$rule];
            }
        }
        return $asserts;
    }
}