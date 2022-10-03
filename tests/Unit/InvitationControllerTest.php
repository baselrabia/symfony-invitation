<?php

namespace App\Tests\Unit;

use App\Tests\BaseWebTestCase;

class InvitationControllerTest extends BaseWebTestCase
{
    public function test_List_send_Invitations(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);
        $crawler = $client->request('GET', '/invitation/');

        $response = $client->getResponse();
        $responseData = $response->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("Retrieve Successfully", $responseData);

    }

    public function test_send_Invitation(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);

        $crawler = $client->request(
            'POST',
            '/invitation/',
            [
                'title' => "party invitation",
                'content' => "we would like to invite you for our party",
                'invite' => "invited@gmail.com",
            ]
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("Created Successfully", $responseData);
        $this->assertStringContainsString("party invitation", $responseData);

    }

    public function test_get_Invitation(): void
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
            'GET',
            '/invitation/1'
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("Retrieve Successfully", $responseData);

    }

    public function test_get_Invitation_not_found(): void
    {
        $client = static::createClient();

        $client->setServerParameters(["HTTP_login_by"=>"sender@gmail.com"]);

        $crawler = $client->request(
            'GET',
            '/invitation/1'
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(404);
        $this->assertStringContainsString("errors", $responseData);

    }

    public function test_cancel_Invitation(): void
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
            '/invitation/1/cancel'
        );

        $responseData = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("success cancel for invitation", $responseData);
        $this->assertSame("cancel", json_decode($responseData)->data->sender_status);
    }


}
