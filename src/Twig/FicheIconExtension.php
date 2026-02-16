<?php

namespace App\Twig;

use App\Service\FicheIconService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FicheIconExtension extends AbstractExtension
{
    public function __construct(private FicheIconService $ficheIconService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('fiche_icon_url', [$this, 'getFicheIconUrl']),
            new TwigFunction('fiche_icon_exists', [$this, 'ficheIconExists']),
            new TwigFunction('fiche_icon_label', [$this, 'getFicheIconLabel']),
        ];
    }

    public function getFicheIconUrl(string $iconName): string
    {
        return $this->ficheIconService->getIconUrl($iconName);
    }

    public function ficheIconExists(string $iconName): bool
    {
        return $this->ficheIconService->iconExists($iconName);
    }

    public function getFicheIconLabel(string $iconName): ?string
    {
        return $this->ficheIconService->getIconLabel($iconName);
    }
}