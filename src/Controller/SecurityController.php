<?php

namespace App\Controller;

use App\Controller\GlobalController;
use App\Repository\LoginErrorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends GlobalController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,EntityManagerInterface $em,  LoginErrorRepository $loginErrorRepo): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $waiting = $this->getParameter('app.time_after_error_login');
        $blockLogin = false;
        
        if ($loginErrorRepo->findAll()) {
            $loginError = $loginErrorRepo->findBy(array(),array('id'=>'DESC'),1,0);
            // block login here
            if ($loginError[0]->getToBeBlocked()) {
                $t = $this->spentTime($loginError[0]->getTimestamp());
                if ($t >= $waiting) {
                    $this->delLoginError($em, $loginErrorRepo->findAll());
                    $this->upLoginError($em, $lastUsername, 0, true);
                    $blockLogin = false;
                } else {
                    $blockLogin = true;
                }
            }
            
            $nbFailure = $loginError[0]->getNbFailure();
        } else {
            $loginError = [];
            $nbFailure = 0;
        }

        if ($error) {
            $this->upLoginError($em, $lastUsername, $nbFailure+1);
            if ($nbFailure >= 5) {
                $this->delLoginError($em, $loginErrorRepo->findAll());
                $this->upLoginError($em, $lastUsername, 0, true);
            }

        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'block_login' => $blockLogin
        ]);
    }

    private function spentTime($tstamp) 
    {
        $s_now = explode(" ", $this->whatTimeIsIt("r"));
        $s_latest = explode(" ", $tstamp);

        $now = substr($s_now[4], 0, 5);
        $latest = substr($s_latest[1], 0, 5);
        
        $start_time = new \DateTime($latest);
        $end_time = new \DateTime($now);
        $time_diff = date_diff($start_time,$end_time);

        return (int)$time_diff->format('%i');
    }

    protected function whatTimeIsIt($format) 
    {
        $now = time(); //current time stamp

        $date = new \DateTime();
        $date->setTimeStamp($now);

        //ISO8601 formatted datetime
        return $date->format($format);
                        
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
