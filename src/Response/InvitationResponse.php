<?php

namespace App\Response;

class InvitationResponse
{

    public static function Collection($data): array
    {
        $collection = [];
        foreach ($data as $key =>  $resource){
            $collection[] =  self::Resource($resource);
        }
        return $collection;
    }

    public static function Resource($invitation): array
    {

        return [
            "id" => $invitation->getId(),
            "title" => $invitation->getTitle(),
            "content" => $invitation->getContent(),
            "sender" => $invitation->getSender()->getEmail(),
            "invited" => $invitation->getInvited()->getEmail(),
            "sender_status" => $invitation->getSenderStatus(),
            "invited_status" => $invitation->getInvitedStatus(),
            "created_at" => $invitation->getCreatedAt()->format('Y-m-d H:i:s'),
            "updated_at" => $invitation->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

    }

}