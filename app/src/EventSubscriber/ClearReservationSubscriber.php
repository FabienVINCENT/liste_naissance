<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

/**
 * Permet de reset les champs de réservation.
 */
class ClearReservationSubscriber implements EventSubscriberInterface
{
    public function onWorkflowArticlesReservationTransitionClearReservation(TransitionEvent $event): void
    {
        /** @var Article $article */
        $article = $event->getSubject();
        $article->setReservedBy(null)
            ->setReservedText(null)
            ->setReservedAt(null);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.articles_reservation.transition.clear_reservation' => 'onWorkflowArticlesReservationTransitionClearReservation',
        ];
    }
}
