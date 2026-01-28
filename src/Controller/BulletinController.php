<?php

namespace App\Controller;

use App\Entity\Student;
use App\Service\BulletinService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BulletinController extends AbstractController
{
    public function __construct(
        private DompdfWrapperInterface $wrapper,
        private BulletinService $bulletinService
    ) {}

    #[Route('/bulletin/{id}', name: 'bulletin_download')]
    public function download(Student $student): Response
    {
        $bulletinData = $this->bulletinService->getBulletinData($student);
        
        $html = $this->renderView('bulletin/report.html.twig', $bulletinData);
        
        $filename = sprintf(
            'bulletin_%s_%s_%s.pdf',
            str_replace(' ', '_', (string) $student->getLastname()),
            str_replace(' ', '_', (string) $student->getFirstname()),
            (new \DateTime())->format('Y-m-d')
        );
        
        return $this->wrapper->getStreamResponse($html, $filename);
    }
    
    #[Route('/bulletin/{id}/preview', name: 'bulletin_preview')]
    public function preview(Student $student): Response
    {
        $bulletinData = $this->bulletinService->getBulletinData($student);
        
        return $this->render('bulletin/preview.html.twig', $bulletinData);
    }
}