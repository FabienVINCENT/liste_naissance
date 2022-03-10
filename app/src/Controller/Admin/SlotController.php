<?php

namespace App\Controller\Admin;

use App\Entity\Texte;
use App\Form\TexteType;
use App\Repository\TexteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/slot', name: 'admin_slot')]
class SlotController extends AbstractController
{
    public function __construct(
        private TexteRepository $texteRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('admin/slot/index.html.twig', [
            'slots' => $this->texteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new')]
    public function new(Request $request): Response|RedirectResponse
    {
        $texte = new Texte();

        return $this->handleForm($texte, $request);
    }

    #[Route('/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function edit(Texte $texte, Request $request): Response|RedirectResponse
    {
        return $this->handleForm($texte, $request);
    }

    private function handleForm(Texte $texte, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(TexteType::class, $texte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($texte);
            $this->entityManager->flush();

            $this->addFlash('success', 'Texte ajouté.');

            return $this->redirectToRoute('admin_slot_index');
        }

        return $this->render('admin/slot/slot.html.twig', ['form' => $form->createView()]);
    }
}
