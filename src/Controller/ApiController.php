<?php
namespace App\Controller;

 use App\Traits\getUserTrait;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\RequestStack;

 class ApiController extends AbstractController
{
    use getUserTrait;

    protected $authUser;
     /**
      * @var Request
      */
     private $request;


     public function Authorize($request)
     {
         $this->request= $request;
         $this->checkAuthUser();
     }

     public function checkAuthUser()
     {
        $login_by = $this->request->headers->get('login_by');

         if(!$login_by){
             $response = $this->respondUnauthorized('Missed Header Field \'login_by\' to authenticate a user');
             $response->send();
         }

         $this->authUser = $this->getUserWithEmail($login_by);
     }

     /**
      * @var integer HTTP status code - 200 (OK) by default
      */
     protected $statusCode = 200;

     /**
      * Gets the value of statusCode.
      *
      * @return integer
      */
     public function getStatusCode()
     {
         return $this->statusCode;
     }

     /**
      * Sets the value of statusCode.
      *
      * @param integer $statusCode the status code
      *
      * @return self
      */
     protected function setStatusCode($statusCode)
     {
         $this->statusCode = $statusCode;

         return $this;
     }

     /**
      * Returns a JSON response
      *
      * @param array $data
      * @param array $headers
      *
      * @return JsonResponse
      */
     public function respond($data, $headers = [])
     {
         return new JsonResponse($data, $this->getStatusCode(), $headers);
     }

     /**
      * Sets an error message and returns a JSON response
      *
      * @param string $errors
      *
      * @return JsonResponse
      */
     public function respondWithErrors($errors, $headers = [])
     {
         $data = [
             'errors' => $errors,
         ];

         return new JsonResponse($data, $this->getStatusCode(), $headers);
     }

     /**
      * Returns a 401 Unauthorized http response
      *
      * @param string $message
      *
      * @return JsonResponse
      */
     public function respondUnauthorized($message = 'Not authorized!')
     {
         return $this->setStatusCode(401)->respondWithErrors($message);
     }

     /**
      * Returns a 422 Unprocessable Entity
      *
      * @param string $message
      *
      * @return JsonResponse
      */
     public function respondValidationError($message = 'Validation errors')
     {
         return $this->setStatusCode(422)->respondWithErrors($message);
     }

     /**
      * Returns a 404 Not Found
      *
      * @param string $message
      *
      * @return JsonResponse
      */
     public function respondNotFound($message = 'Not found!')
     {
         return $this->setStatusCode(404)->respondWithErrors($message);
     }

     /**
      * Returns a 201 Created
      *
      * @param array $data
      *
      * @return JsonResponse
      */
     public function respondCreated($data = [])
     {
         return $this->setStatusCode(201)->respond($data);
     }



 }