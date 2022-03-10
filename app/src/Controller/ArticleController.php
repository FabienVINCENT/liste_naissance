<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/article', name: 'app_article_')]
class ArticleController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private SerializerInterface $serializer,
        private WorkflowInterface $articlesReservationStateMachine
    ) {
    }

    #[Route('/{id}', name: 'reserve', requirements: ['id' => '\d+'], methods: 'POST')]
    public function reserve(Article $article, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);

            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new RuntimeException("L'utilisateur n'est pas correct.");
            }

            if ($article->getReservedBy()) {
                throw new RuntimeException('Cet article est déjà réservé', Response::HTTP_CONFLICT);
            }

            if (!$this->articlesReservationStateMachine->can($article, 'to_reserved')) {
                throw new RuntimeException('Cet article ne peux pas être réservé.');
            }

            $article
                ->setReservedBy($user)
                ->setReservedBy($user)
                ->setReservedText($data->message);

            $this->articlesReservationStateMachine->apply($article, 'to_reserved');

            $entityManager->flush();

            return $this->json([], Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->jsonException($e);
        }
    }

    #[Route('', name: 'search', methods: ['GET', 'POST'])]
    public function search(Request $request, ArticleRepository $articleRepository, TagRepository $tagRepository): Response
    {
        $form = $this->createForm(SearchArticleType::class);
        $search = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articles = $articleRepository->search($search->get('search')->getData());
        } else {
            $articles = $articleRepository->findAll();
            shuffle($articles);
        }

        return $this->render('home/search.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'tags' => $tagRepository->findAll(),
        ]);
    }
}
