<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\GuardEvent;

class CancelReservationGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security)
    {
    }

    public function onWorkflowArticlesReservationGuardClearReservation(GuardEvent $event)
    {
        /** @var Article $article */
        $article = $event->getSubject();
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            if (
                !$this->security->getUser() ||
                !$article->getReservedBy() ||
                $this->security->getUser()->getUserIdentifier() !== $article->getReservedBy()->getUserIdentifier()
            ) {
                $event->setBlocked(true, 'Annulation impossible');
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.articles_reservation.guard.clear_reservation' => 'onWorkflowArticlesReservationGuardClearReservation',
        ];
    }
}
