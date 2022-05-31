<?php

namespace App\Controller;

use App\Entity\LoginError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GlobalController extends AbstractController
{
    public function upLoginError(EntityManagerInterface $em, $email, $nbFailure, $toBeBlocked = false)
    {
        $dTime = new \DateTime();

        $loginError = new LoginError();

        $loginError->setEmail($email);
        $loginError->setNbFailure($nbFailure);
        $loginError->setToBeBlocked($toBeBlocked);
        $loginError->setTimestamp($dTime->format('Y-m-d H:i:s'));

        $em->persist($loginError);
        $em->flush();
    }

    public function delLoginError($em, $loginErrorAll)
    {
        foreach ($loginErrorAll as $loginError) {
            $em->remove($loginError);
        }
        $em->flush();
    }
}
