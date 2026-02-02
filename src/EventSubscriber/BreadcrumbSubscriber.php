<?php

namespace App\EventSubscriber;

use App\Service\BreadcrumbService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BreadcrumbSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private BreadcrumbService $breadcrumbService
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->breadcrumbService->reset();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
