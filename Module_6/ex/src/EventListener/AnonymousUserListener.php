<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AnonymousUserListener
{
    private UrlGeneratorInterface $urlGenerator;
    private Security $security;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if (!$session) {
            return;
        }

        // Vérifie si l'utilisateur est connecté
        $user = $this->security->getUser();

        if ($user) {
            // ✅ L'utilisateur est authentifié → Met à jour l'interaction sans l'invalider
            $session->set('lastInteraction', time());
            return;
        }

        // 🔹 Gère les utilisateurs anonymes
        if (!$session->has('anonymousName')) {
            $session->set('anonymousName', $this->getRandomAnonymousName());
            $session->set('lastInteraction', time());
        } else {
            $lastInteraction = $session->get('lastInteraction', time());
            $timeElapsed = time() - $lastInteraction;

            if ($timeElapsed > 60) {
                $session->invalidate();
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
            } else {
                $session->set('lastInteraction', time());
            }
        }
    }

    private function getRandomAnonymousName(): string
    {
        $animals = ['dog', 'cat', 'rabbit', 'fox', 'bear', 'lion', 'tiger', 'wolf', 'eagle'];
        $randomAnimal = $animals[array_rand($animals)];
        return 'Anonymous ' . ucfirst($randomAnimal);
    }
}

