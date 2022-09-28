<?php

namespace App\Request;

class StoreInvitationRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'title' => ['Required', 'NotBlank', 'String'],
            'content' => ['Required', 'NotBlank', 'String'],
            'invite' => ['Required', 'NotBlank', 'String', 'Email'],
        ];
    }

}