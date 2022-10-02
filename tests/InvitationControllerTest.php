<?php

namespace App\Tests;

use App\Response\InvitationResponse;
use App\Tests\AppBundle\DatabasePrimer;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvitationControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void     {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

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


        $response = $client->getResponse();
        $responseData = $response->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("party invitation", $responseData);

    }
}
