<?php

namespace App\Controller;

use App\Entity\Groupe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GroupeController extends AbstractController
{
    #[Route('/groupes/nouveau', name: 'groupe_new')]
    public function new(Request $request): Response
    {
        return $this->render('groupe/new.html.twig');
    }
}
