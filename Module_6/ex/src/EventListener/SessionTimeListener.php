<?php
// src/EventListener/SessionTimeListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionTimeListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        if ($session->isStarted()) {
            // Si l'utilisateur n'est pas connecté (anonyme)
            if (!$this->security->getUser()) {
                // Vérifie si la dernière interaction date de plus d'une minute
                $lastInteraction = $session->get('lastInteraction', time());
                if (time() - $lastInteraction > 60) {
                    $session->invalidate();
                    $session->start();
                }
                $session->set('lastInteraction', time());
            }
        }
    }
}