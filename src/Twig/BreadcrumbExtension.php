<?php

namespace App\Twig;

use App\Service\BreadcrumbService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class BreadcrumbExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private BreadcrumbService $breadcrumbService
    ) {}

    public function getGlobals(): array
    {
        return [
            'breadcrumbs' => $this->breadcrumbService->all(),
        ];
    }
}