<?php

namespace App\Controller;

use App\Entity\Student;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BulletinController extends AbstractController
{
    public function __construct(
        private DompdfWrapperInterface $wrapper
    )
    {}

    #[Route('/bulletin/{id}', name: 'dl_bulletin')]
    public function index(Student $student): Response
    {
        $html = $this->render('bulletin/index.html.twig', [
            'controller_name' => 'BulletinController',
        ]);

        return $this->wrapper->getStreamResponse($html, 'releve_de-' . $student->getId() . '.pdf');
    }
}
