<?php

namespace App\Controller;

use App\Repository\InvitationRepository;
use App\Response\InvitationResponse;
use App\Service\InvitationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class InvitedController extends ApiController
{

    private $invitationService;

    public function __construct(InvitationService $invitationRepository)
    {
        $this->invitationService = $invitationRepository;
    }

    /**
     * @Route("/invitations/received", name="List received Invitations", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $this->Authorize();

        $data = $this->invitationService->listInvitedInvitations($this->authUser, "all");

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
        $this->Authorize();

        try {
            $data = $this->invitationService->acceptInvitation($this->authUser, $id);
        } catch (\Exception $e) {
            return $this->setStatusCode($e->getStatusCode())
                ->respondWithErrors($e->getMessage());
        }

        return $this->json([
            'message' => 'success cancel for invitation ' . $id,
            'data' => InvitationResponse::Resource($data),
        ]);
    }

    /**
     * @Route("/invitation/{id}/reject", methods="PATCH", name="reject Invitation" )
     */
    public function reject($id): Response
    {
        $this->Authorize();

        try {
            $data = $this->invitationService->rejectInvitation($this->authUser, $id);
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
