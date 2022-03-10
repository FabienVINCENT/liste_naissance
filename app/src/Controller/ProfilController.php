<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ProfilType;
use App\Repository\TexteRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if (!$user instanceof User) {
            throw new LogicException('Le user n\'est pas un user');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $profil = $data['profil'];
            $user->setFirstname($profil['firstname']);
            $user->setLastname($profil['lastname']);
            $entityManager->flush();
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservations', name: 'app_profil_reservations')]
    public function reservations(TexteRepository $texteRepository): Response
    {
        $reservation = $texteRepository->findOneBy(['slot' => 'reservation']);

        return $this->render('profil/reservations.html.twig', [
            'user' => $this->getUser(),
            'reservation_slot' => $reservation ? $reservation->getMessage() : 'slot : reservation',
        ]);
    }

    #[Route('/reservations/cancel/{id}', name: 'app_profil_cancel_reservation')]
    public function cancelReservations(
        Article $article,
        EntityManagerInterface $entityManager,
        WorkflowInterface $articlesReservationStateMachine,
        TranslatorInterface $translator
    ): RedirectResponse {
        if ($articlesReservationStateMachine->can($article, 'clear_reservation')) {
            $articlesReservationStateMachine->apply($article, 'clear_reservation');
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('clear_reservation_success'));
        } else {
            $this->addFlash('error', $translator->trans('clear_reservation_error'));
        }

        return $this->redirectToRoute('app_profil_reservations');
    }
}
