<?php

namespace App\Controller;

use App\Entity\Choix;
use App\Repository\ChoixRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChoixController extends AbstractController
{
    /**
     * Ajouter un choix à une question (utile surtout en mode edit via une petite modale / requête POST).
     * POST: libelle, estCorrect (0/1)
     */
    #[Route('/admin/question/{questionId}/choix/add', name: 'admin_choix_add', methods: ['POST'])]
    public function add(
        int $questionId,
        Request $request,
        EntityManagerInterface $em,
        QuestionRepository $questionRepository
    ): Response {
        $question = $questionRepository->find($questionId);
        if (!$question) {
            throw $this->createNotFoundException('Question introuvable');
        }

        $libelle = trim((string) $request->request->get('libelle', ''));
        $estCorrect = (bool) $request->request->get('estCorrect', false);

        if ($libelle === '') {
            $this->addFlash('error', 'Le libellé du choix est obligatoire.');
            return $this->redirectToRoute('admin_question_edit', ['id' => $question->getId()]);
        }

        $choix = new Choix();
        $choix->setLibelle($libelle);
        $choix->setEstCorrect($estCorrect);
        $choix->setQuestion($question);

        $em->persist($choix);
        $em->flush();

        $this->addFlash('success', 'Choix ajouté avec succès.');
        return $this->redirectToRoute('admin_question_edit', ['id' => $question->getId()]);
    }

    /**
     * Supprimer un choix existant.
     * Important : vérifie que le choix appartient bien à la question passée dans l'URL.
     */
    #[Route('/admin/question/{questionId}/choix/{choixId}/delete', name: 'admin_choix_delete', methods: ['POST'])]
    public function delete(
        int $questionId,
        int $choixId,
        Request $request,
        EntityManagerInterface $em,
        QuestionRepository $questionRepository,
        ChoixRepository $choixRepository
    ): Response {
        $question = $questionRepository->find($questionId);
        if (!$question) {
            throw $this->createNotFoundException('Question introuvable');
        }

        $choix = $choixRepository->find($choixId);
        if (!$choix) {
            throw $this->createNotFoundException('Choix introuvable');
        }

        // Vérifier appartenance
        if (!$choix->getQuestion() || $choix->getQuestion()->getId() !== $question->getId()) {
            throw $this->createAccessDeniedException('Ce choix ne correspond pas à cette question.');
        }

        // (Optionnel) CSRF token check
        // $token = $request->request->get('_token');
        // if (!$this->isCsrfTokenValid('delete_choix_'.$choix->getId(), $token)) {
        //     throw $this->createAccessDeniedException('CSRF token invalide.');
        // }

        $em->remove($choix);
        $em->flush();

        $this->addFlash('success', 'Choix supprimé.');
        return $this->redirectToRoute('admin_question_edit', ['id' => $question->getId()]);
    }

    /**
     * Basculer "bonne réponse" (toggle).
     * Pratique en édition rapide.
     */
    #[Route('/admin/question/{questionId}/choix/{choixId}/toggle-correct', name: 'admin_choix_toggle_correct', methods: ['POST'])]
    public function toggleCorrect(
        int $questionId,
        int $choixId,
        EntityManagerInterface $em,
        QuestionRepository $questionRepository,
        ChoixRepository $choixRepository
    ): Response {
        $question = $questionRepository->find($questionId);
        if (!$question) {
            throw $this->createNotFoundException('Question introuvable');
        }

        $choix = $choixRepository->find($choixId);
        if (!$choix) {
            throw $this->createNotFoundException('Choix introuvable');
        }

        if (!$choix->getQuestion() || $choix->getQuestion()->getId() !== $question->getId()) {
            throw $this->createAccessDeniedException('Ce choix ne correspond pas à cette question.');
        }

        $choix->setEstCorrect(!$choix->isEstCorrect());
        $em->flush();

        $this->addFlash('success', 'Statut de la bonne réponse mis à jour.');
        return $this->redirectToRoute('admin_question_edit', ['id' => $question->getId()]);
    }
}
