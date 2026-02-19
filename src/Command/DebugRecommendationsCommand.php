<?php

namespace App\Command;

use App\Service\OrientationRecommenderService;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:debug-recommendations',
    description: 'Debug the recommendation system'
)]
class DebugRecommendationsCommand extends Command
{
    public function __construct(
        private OrientationRecommenderService $recommenderService,
        private UtilisateurRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get a test user
        $user = $this->userRepository->findOneBy([]);
        if (!$user) {
            $io->error('No users found in database');
            return Command::FAILURE;
        }

        // Test scores for Sciences Expérimentales
        $testScores = [
            'FG' => 145.5,
            'M' => 16.5,
            'PH' => 15.2,
            'SVT' => 14.8,
            'A' => 12.0,
            'Ang' => 13.5,
            'F' => 11.8,
            'HG' => 12.5,
            'SP' => 14.0
        ];

        $io->title('Testing Recommendation System');
        $io->section('Test User: ' . $user->getEmail());
        $io->section('Test Scores:');
        foreach ($testScores as $subject => $score) {
            $io->writeln("$subject: $score");
        }

        try {
            $recommendations = $this->recommenderService->getRecommendations($user, $testScores, [
                'limit' => 20
            ]);

            $io->section('Results:');
            $io->writeln('Total recommendations: ' . count($recommendations));

            if (empty($recommendations)) {
                $io->error('No recommendations found!');
                return Command::FAILURE;
            }

            foreach (array_slice($recommendations, 0, 5) as $i => $rec) {
                $io->writeln(sprintf(
                    '%d. %s (T: %s, Cutoff: %s, Margin: %s, Final Score: %s)',
                    $i + 1,
                    $rec['program']->getDisplayName(),
                    $rec['tUser'],
                    $rec['cutoff2024'],
                    $rec['margin'],
                    round($rec['finalScore'], 3)
                ));
            }

            $io->success('Recommendation system is working!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            $io->writeln('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}