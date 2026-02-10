<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Si l'utilisateur est admin, rediriger vers la page d'administration
        if ($user->isAdmin()) {
            $response = new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
            $event->setResponse($response);
        }
        // Sinon, laisser la redirection par défaut (app_home)
    }
}
