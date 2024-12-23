<?php


namespace App\Tests\Controllers\MessagesControllerTest;

use App\Entity\Messages;
use App\Entity\Users;
use App\Tests\TestCase\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DeleteMessagesControllerTest extends WebTestCase
{

    public function testBadResponseDelete()
    {
        $client  = $this->createAuthenticatedApiClient();

        $client->request('DELETE', '/api/messages2',[], [], []);
        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        var_dump($data);
    }

    public function testResponseDeleteMessages()
    {
        $client  = $this->createAuthenticatedApiClient();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $repositoryUsers = $entityManager->getRepository(Users::class);
        $repositoryMessages = $entityManager->getRepository(Messages::class);
        $sender = $repositoryUsers->findOneBy(['name' => 'test1']);
        $senderId = $sender->getId();
        $messages = $repositoryMessages->findOneBy(['sender' => $senderId]);
        $messagesId = $messages->getId();

        $client->request('DELETE', '/api/messages' . $messagesId,[], [], []);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        var_dump($data);

    }

}