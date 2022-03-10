<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

/**
 * Permet de reset les champs de réservation.
 */
class ToReservedSubscriber implements EventSubscriberInterface
{
    public function onWorkflowArticlesReservationTransitionToReserved(TransitionEvent $event): void
    {
        /** @var Article $article */
        $article = $event->getSubject();
        $article->setReservedAt(new DateTimeImmutable());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.articles_reservation.transition.to_reserved' => 'onWorkflowArticlesReservationTransitionToReserved',
        ];
    }
}
