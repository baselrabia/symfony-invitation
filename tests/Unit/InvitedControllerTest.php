<?php

namespace App\Tests\Unit;

use App\Tests\BaseWebTestCase;

class InvitedControllerTest extends BaseWebTestCase
{
    public function test_List_received_Invitations(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);
        $crawler = $client->request(
            'GET',
            '/invitations/received'
        );

        $response = $client->getResponse();
        $responseData = $response->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("Retrieve Successfully", $responseData);

    }

    public function test_accept_Invitation(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);

        $client->request(
            'POST',
            '/invitation/',
            [
                'title' => "party invitation",
                'content' => "we would like to invite you for our party",
                'invite' => "invited@gmail.com",
            ]
        );
        $crawler = $client->request(
            'PATCH',
            '/invitation/1/accept'
        );

        $responseData = $client->getResponse()->getContent();
        $this->assertStringContainsString("unable to Accept the invitation", $responseData);

        $client->setServerParameters(["HTTP_login_by"=>"invited@gmail.com"]);

        $crawler = $client->request(
            'PATCH',
            '/invitation/1/accept'
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("success accept for invitation", $responseData);
        $this->assertSame("accept", json_decode($responseData)->data->invited_status);
    }



    public function test_reject_Invitation(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);

        $client->request(
            'POST',
            '/invitation/',
            [
                'title' => "party invitation",
                'content' => "we would like to invite you for our party",
                'invite' => "invited@gmail.com",
            ]
        );
        $crawler = $client->request(
            'PATCH',
            '/invitation/1/reject'
        );

        $responseData = $client->getResponse()->getContent();
        $this->assertStringContainsString("unable to Reject the invitation", $responseData);

        $client->setServerParameters(["HTTP_login_by"=>"invited@gmail.com"]);

        $crawler = $client->request(
            'PATCH',
            '/invitation/1/reject'
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("success reject for invitation", $responseData);
        $this->assertSame("reject", json_decode($responseData)->data->invited_status);
    }

}
