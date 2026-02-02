<?php

namespace App\Service;

/**
 * @phpstan-type BreadcrumbItem array{
 *     label: string,
 *     url: string|null
 * }
 */
class BreadcrumbService
{
    /**
     * @var BreadcrumbItem[]
     */
    private array $items = [];

    public function add(string $label, ?string $url = null): self
    {
        $this->items[] = [
            'label' => $label,
            'url' => $url,
        ];

        return $this;
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function all(): array
    {
        return $this->items;
    }

    public function reset(): void
    {
        $this->items = [];
    }
}
