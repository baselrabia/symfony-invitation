<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\InvitationRepository;
use App\Request\StoreInvitationRequest;
use App\Response\InvitationResponse;
use App\Service\InvitationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/invitation")
 */
class InvitationController extends ApiController
{

    private $invitationService;
    /**
     * @var Request|null
     */
    private $request;


    public function __construct(InvitationService $invitationRepository, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->invitationService = $invitationRepository;
    }

    /**
     * @Route("/", methods="GET", name="List send Invitations")
     */
    public function index(): JsonResponse
    {
        $this->Authorize($this->request);

        $data = $this->invitationService->listSenderInvitations($this->authUser, $_GET['status'] ?? "all");

        return $this->json([
            'message' => 'Retrieve Successfully',
            'data' => InvitationResponse::Collection($data),
        ]);
    }

    /**
     * @Route("/", methods="POST", name="Send Invitation" )
     */
    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $this->Authorize($this->request);

        try {
            $data = $this->invitationService->sendInvitation($this->authUser, $request->all());
        } catch (Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'Created Successfully',
            'data' => InvitationResponse::Resource($data),
        ]);
    }


    /**
     * @Route("/{id}", methods="GET", name="get Invitation" )
     */
    public function show($id): JsonResponse
    {
        $this->Authorize($this->request);

        try {
            $data = $this->invitationService->getInvitation($this->authUser, $id);
        } catch (\Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'Retrieve Successfully',
            'data' => InvitationResponse::Resource($data),
        ]);
    }

    /**
     * @Route("/{id}/cancel", methods="PATCH", name="cancel Invitation" )
     */
    public function cancel($id): JsonResponse
    {
        $this->Authorize($this->request);

        try {
            $data = $this->invitationService->cancelInvitation($this->authUser, $id);
        } catch (\Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'success cancel for invitation ' . $id,
            'data' => InvitationResponse::Resource($data),
        ]);
    }


}
