<?php

namespace App\Command;

use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-programs',
    description: 'Import university programs from CSV file'
)]
class ImportProgramsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $csvFile = getcwd() . '/orientation_guide_2025_full.csv';
        
        if (!file_exists($csvFile)) {
            $io->error('CSV file not found: ' . $csvFile);
            return Command::FAILURE;
        }

        $io->title('Importing University Programs');
        
        // Clear existing programs
        $io->section('Clearing existing programs...');
        $this->entityManager->createQuery('DELETE FROM App\Entity\Program')->execute();
        
        // Open CSV file
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $io->error('Cannot open CSV file');
            return Command::FAILURE;
        }

        // Skip header row
        $header = fgetcsv($handle);
        $io->note('CSV Headers: ' . implode(', ', $header));

        $imported = 0;
        $skipped = 0;
        $batchSize = 100;

        $io->section('Processing CSV data...');
        $io->progressStart();

        while (($data = fgetcsv($handle)) !== false) {
            $io->progressAdvance();
            
            // Skip empty or invalid rows
            if (count($data) < 9 || empty($data[1]) || empty($data[7])) {
                $skipped++;
                continue;
            }

            // Skip header-like rows
            if (strpos($data[7], 'الاطلاععلىالصيغةالاجمالية') !== false) {
                $skipped++;
                continue;
            }

            $program = new Program();
            $program->setDomainAr(trim($data[0]) ?: null);
            $program->setProgramNameAr(trim($data[1]));
            $program->setSpecializationAr(trim($data[2]) ?: null);
            $program->setProgramCode(trim($data[3]) ?: null);
            $program->setInstitutionAr(trim($data[4]) ?: null);
            $program->setUniversityAr(trim($data[5]) ?: null);
            $program->setBacTypeAr(trim($data[6]) ?: null);
            $program->setFormulaT(trim($data[7]));
            
            // Parse cutoff score
            $cutoffStr = trim($data[8]);
            if (!empty($cutoffStr) && is_numeric($cutoffStr)) {
                $program->setCutoff2024((float) $cutoffStr);
            }

            // Only save valid programs
            if ($program->isValid()) {
                $this->entityManager->persist($program);
                $imported++;

                // Flush in batches
                if ($imported % $batchSize === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            } else {
                $skipped++;
            }
        }

        // Final flush
        $this->entityManager->flush();
        $this->entityManager->clear();
        
        fclose($handle);
        $io->progressFinish();

        $io->success([
            'Import completed!',
            "Imported: $imported programs",
            "Skipped: $skipped rows"
        ]);

        // Show some statistics
        $this->showImportStats($io);

        return Command::SUCCESS;
    }

    private function showImportStats(SymfonyStyle $io): void
    {
        $io->section('Import Statistics');

        // Count by BAC type
        $bacTypeCounts = $this->entityManager->getRepository(Program::class)->countByBacType();
        
        $io->table(['BAC Type', 'Count'], array_map(function($type, $count) {
            return [$type, $count];
        }, array_keys($bacTypeCounts), $bacTypeCounts));

        // Cutoff statistics
        $cutoffStats = $this->entityManager->getRepository(Program::class)->getCutoffStats();
        
        $io->table(['Statistic', 'Value'], [
            ['Min Cutoff', number_format($cutoffStats['min'], 2)],
            ['Max Cutoff', number_format($cutoffStats['max'], 2)],
            ['Avg Cutoff', number_format($cutoffStats['avg'], 2)]
        ]);

        // Top universities by program count
        $universities = $this->entityManager->getRepository(Program::class)->getUniversities();
        $io->note('Total universities: ' . count($universities));
    }
}