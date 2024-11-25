<?php


namespace App\Controller\Reset;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\VeryfiMailCode;
use App\Repository\UsersRepository;
use App\Command\Update\UpdatePassword\UpdatePasswordCommand;



class ResetPasswordController extends AbstractController
{
    private UpdatePasswordCommand $updatePassword;
    private UsersRepository $userRepository;
    private VeryfiMailCode $veryfiMail;

    public function __construct
    (
        UpdatePasswordCommand $updatePassword,
        UsersRepository $userRepository,
        VeryfiMailCode $veryfiMail
    )
    {
        $this->updatePassword = $updatePassword;
        $this->userRepository = $userRepository;
        $this->veryfiMail = $veryfiMail;
    }

    #[Route('/api/reset/password/reset', name: 'ResetPasswordResert', methods: ['POST'])]
    public function reset(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $code = $data['code'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        $isVerifiedMail = $this->veryfiMail->veryfi($email, $code);

        if ($isVerifiedMail) {
            $setPassword = $this->updatePassword->updatePass($email, $newPassword);
            if ($setPassword) {
                return new JsonResponse('Пароль успешно изменён', 201);
            }
        } else {
            return new JsonResponse('Пользователь с такой почтой не существует', 404);
        }


        return new JsonResponse('Не удалось обработать запрос', 400);
    }

}