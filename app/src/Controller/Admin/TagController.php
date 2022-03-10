<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/tag', name: 'admin_tag')]
class TagController extends AbstractController
{
    public function __construct(private TagRepository $tagRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('admin/tag/index.html.twig', [
            'tags' => $this->tagRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new')]
    public function new(Request $request): Response|RedirectResponse
    {
        $tag = new Tag();

        return $this->handleForm($tag, $request);
    }

    #[Route('/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function edit(Tag $tag, Request $request): Response|RedirectResponse
    {
        return $this->handleForm($tag, $request);
    }

    private function handleForm(Tag $tag, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tag ajouté.');

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('admin/tag/tag.html.twig', ['form' => $form->createView()]);
    }
}
