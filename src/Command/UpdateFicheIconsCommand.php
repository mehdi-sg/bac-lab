<?php

namespace App\Command;

use App\Entity\Fiche;
use App\Service\FicheIconService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-fiche-icons',
    description: 'Add sample icons to existing fiches for testing',
)]
class UpdateFicheIconsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FicheIconService $ficheIconService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $ficheRepository = $this->entityManager->getRepository(Fiche::class);
        $fiches = $ficheRepository->findAll();
        
        if (empty($fiches)) {
            $io->warning('No fiches found in database.');
            return Command::SUCCESS;
        }
        
        $availableIcons = array_keys($this->ficheIconService->getAvailableIcons());
        $iconCount = count($availableIcons);
        
        $updated = 0;
        foreach ($fiches as $index => $fiche) {
            // Only update fiches that don't have an icon yet
            if (!$fiche->getIcon()) {
                // Assign icons based on title keywords or cyclically
                $icon = $this->selectIconForFiche($fiche, $availableIcons, $index % $iconCount);
                $fiche->setIcon($icon);
                $updated++;
            }
        }
        
        $this->entityManager->flush();
        
        $io->success(sprintf('Updated %d fiches with icons.', $updated));
        
        return Command::SUCCESS;
    }
    
    private function selectIconForFiche(Fiche $fiche, array $availableIcons, int $defaultIndex): string
    {
        $title = strtolower($fiche->getTitle());
        
        // Try to match icon based on title keywords
        if (str_contains($title, 'math') || str_contains($title, 'calcul') || str_contains($title, 'équation')) {
            return 'mathematique.png';
        }
        
        if (str_contains($title, 'science') || str_contains($title, 'physique') || str_contains($title, 'chimie') || str_contains($title, 'biologie')) {
            return 'scieneces-expérimentale.png';
        }
        
        if (str_contains($title, 'lettre') || str_contains($title, 'français') || str_contains($title, 'littérature') || str_contains($title, 'philosophie')) {
            return 'lettrespng.png';
        }
        
        if (str_contains($title, 'info') || str_contains($title, 'programmation') || str_contains($title, 'code')) {
            return 'informatique.png';
        }
        
        if (str_contains($title, 'économie') || str_contains($title, 'gestion') || str_contains($title, 'commerce')) {
            return 'ecogestion.png';
        }
        
        if (str_contains($title, 'technique') || str_contains($title, 'technologie') || str_contains($title, 'mécanique')) {
            return 'technique.png';
        }
        
        // Default: use cyclic assignment
        return $availableIcons[$defaultIndex];
    }
}