<?php

namespace App\Controller;

use App\Repository\StudentAuditLogRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\StudentAuditLog;

#[Route('/log')]
class LogController extends AbstractController
{
    #[Route('/', name: 'log_index')]
    public function index(Request $request, StudentAuditLogRepository $repo): Response
    {
        $limit = 20;
        $before = $request->query->get('before');

        // ✅ Validate $before
        $beforeDate = null;
        if (is_string($before)) {
            $beforeDate = new \DateTimeImmutable($before);
        }

        $qb = $repo->createQueryBuilder('l')
            ->orderBy('l.timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($beforeDate instanceof \DateTimeImmutable) {
            $qb->andWhere('l.timestamp < :before')
               ->setParameter('before', $beforeDate);
        }

        /** @var StudentAuditLog[] $logs */
        $logs = $qb->getQuery()->getResult();

        // ✅ Get last log safely
        $lastLog = !empty($logs) ? $logs[array_key_last($logs)] : null;
        $logsCount = count($logs);

        return $this->render('log/index.html.twig', [
            'logs' => $logs,
            'lastTimestamp' => $lastLog?->getTimestamp(),
            'hasMore' => $logsCount === $limit, // true if we can load older logs
        ]);
    }
}
