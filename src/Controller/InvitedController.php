<?php

namespace App\Controller;

use App\Repository\InvitationRepository;
use App\Response\InvitationResponse;
use App\Service\InvitationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class InvitedController extends ApiController
{

    private $invitationService;
    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;

    public function __construct(InvitationService $invitationRepository, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->invitationService = $invitationRepository;
    }


    /**
     * @Route("/invitations/received", name="List received Invitations", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $this->Authorize($this->request);

        $data = $this->invitationService->listInvitedInvitations($this->authUser, $_GET['status'] ?? "all");

        return $this->json([
            'message' => 'Retrieve Successfully',
            'data' => InvitationResponse::Collection($data),
        ]);
    }

    /**
     * @Route("/invitation/{id}/accept", methods="PATCH", name="accept Invitation" )
     */
    public function accept($id): Response
    {
        $this->Authorize($this->request);

        try {
            $data = $this->invitationService->acceptInvitation($this->authUser, $id);
        } catch (\Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'success accept for invitation ' . $id,
            'data' => InvitationResponse::Resource($data),
        ]);
    }

    /**
     * @Route("/invitation/{id}/reject", methods="PATCH", name="reject Invitation" )
     */
    public function reject($id): Response
    {
        $this->Authorize($this->request);

        try {
            $data = $this->invitationService->rejectInvitation($this->authUser, $id);
        } catch (\Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'success reject for invitation ' . $id,
            'data' => InvitationResponse::Resource($data),
        ]);
    }
}
