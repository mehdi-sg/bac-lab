<?php

namespace App\Service;

class FicheIconService
{
    private const ICON_PATH = 'front/assets/img/fiche-icon/';
    
    private const AVAILABLE_ICONS = [
        'mathematique.png' => 'Mathématiques',
        'scieneces-expérimentale.png' => 'Sciences Expérimentales', 
        'lettrespng.png' => 'Lettres',
        'informatique.png' => 'Informatique',
        'ecogestion.png' => 'Économie & Gestion',
        'technique.png' => 'Technique',
    ];
    
    public function getAvailableIcons(): array
    {
        return self::AVAILABLE_ICONS;
    }
    
    public function getIconChoices(): array
    {
        return array_flip(self::AVAILABLE_ICONS);
    }
    
    public function getIconPath(): string
    {
        return self::ICON_PATH;
    }
    
    public function getIconUrl(string $iconName): string
    {
        return '/' . self::ICON_PATH . $iconName;
    }
    
    public function iconExists(string $iconName): bool
    {
        return array_key_exists($iconName, self::AVAILABLE_ICONS);
    }
    
    public function getIconLabel(string $iconName): ?string
    {
        return self::AVAILABLE_ICONS[$iconName] ?? null;
    }
}