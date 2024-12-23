<?php


namespace App\Command\Update\UpdateMessages;

use App\Entity\Messages;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MessagesService\EncryptionService;


class UpdateMessages
{

    private EncryptionService $encrypt;
    private EntityManagerInterface $entityManager;

    public function __construct(EncryptionService $encrypt, EntityManagerInterface $entityManager)
    {
        $this->encrypt = $encrypt;
        $this->entityManager = $entityManager;
    }


    public function updateMess($idMessages, $newMessages)
    {

        $isChange = $this->entityManager->getRepository( Messages::class)
            ->findOneBy(['id' => $idMessages]);
        $encryptedMessages = $this->encrypt->encryptMessage($newMessages);
        $isChange->setContent($encryptedMessages['encrypted_message']);
        $isChange->setIv($encryptedMessages['iv']);
        $this->entityManager->persist($isChange);
        $this->entityManager->flush();
        return true;
    }
}