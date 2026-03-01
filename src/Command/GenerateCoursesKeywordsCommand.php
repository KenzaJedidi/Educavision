<?php

namespace App\Command;

use App\Repository\CourseRepository;
use App\Service\OpenAICourseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-keywords',
    description: 'Génère les mots-clés IA pour tous les cours qui n\'en ont pas',
)]
class GenerateCoursesKeywordsCommand extends Command
{
    public function __construct(
        private CourseRepository $courseRepository,
        private OpenAICourseService $openAICourseService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('all', null, InputOption::VALUE_NONE, 'Générer les mots-clés pour TOUS les cours, même ceux qui en ont déjà')
            ->addOption('course-id', 'id', InputOption::VALUE_OPTIONAL, 'Générer les mots-clés pour un cours spécifique par son ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Déterminer quels cours traiter
        if ($input->getOption('course-id')) {
            $courseId = (int)$input->getOption('course-id');
            $course = $this->courseRepository->find($courseId);
            
            if (!$course) {
                $io->error("Cours avec l'ID {$courseId} non trouvé");
                return Command::FAILURE;
            }
            
            $courses = [$course];
            $io->info("Traitement du cours: {$course->getTitre()}");
        } elseif ($input->getOption('all')) {
            $courses = $this->courseRepository->findAll();
            $io->info("Traitement de tous les cours (" . count($courses) . ")");
        } else {
            // Par défaut: seulement les cours sans mots-clés
            $courses = $this->courseRepository->findBy(['keywords' => null]);
            $io->info("Traitement des cours sans mots-clés (" . count($courses) . ")");
        }

        if (count($courses) === 0) {
            $io->success("Aucun cours à traiter");
            return Command::SUCCESS;
        }

        $successCount = 0;
        $failureCount = 0;
        $skippedCount = 0;

        $progressBar = $io->createProgressBar(count($courses));
        $progressBar->start();

        foreach ($courses as $course) {
            $progressBar->advance();

            // Sauter si les mots-clés existent déjà et --all n'est pas activé
            if (!$input->getOption('all') && $course->getKeywords()) {
                $skippedCount++;
                continue;
            }

            try {
                $keywords = $this->openAICourseService->generateKeywords(
                    $course->getTitre(),
                    $course->getDescription(),
                    $course->getCategory()
                );

                if ($keywords) {
                    $course->setKeywords($keywords);
                    $this->entityManager->flush();
                    $successCount++;
                    
                    $io->writeln("");
                    $io->success("✅ Mots-clés générés pour: " . $course->getTitre());
                } else {
                    $failureCount++;
                    $error = $this->openAICourseService->getLastError();
                    
                    $io->writeln("");
                    $io->warning("⚠️  Erreur pour {$course->getTitre()}: {$error}");
                }
            } catch (\Exception $e) {
                $failureCount++;
                
                $io->writeln("");
                $io->error("❌ Exception pour {$course->getTitre()}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $io->writeln("\n");

        // Résumé final
        $io->section("Résumé de l'exécution");
        $io->table(
            ['Statut', 'Nombre'],
            [
                ['✅ Succès', $successCount],
                ['❌ Erreurs', $failureCount],
                ['⏭️  Ignorés', $skippedCount],
                ['📊 Total traité', $successCount + $failureCount],
            ]
        );

        if ($successCount > 0) {
            $io->success("Mots-clés générés avec succès pour {$successCount} cours!");
            return Command::SUCCESS;
        } elseif ($failureCount === 0 && $skippedCount > 0) {
            $io->comment("Tous les cours avaient déjà des mots-clés.");
            return Command::SUCCESS;
        } else {
            $io->error("Erreurs lors de la génération des mots-clés");
            return Command::FAILURE;
        }
    }
}
