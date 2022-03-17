<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\AdminFilterArticleType;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/article', name: 'admin_article')]
class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private EntityManagerInterface $entityManager,
        private string $uploadFolder,
        private SluggerInterface $slugger
    ) {
    }

    #[Route('', name: '_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(AdminFilterArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();
            $filters = [];
            if ($datas['statut']) {
                $filters['status'] = $datas['statut'];
            }
            if ($datas['tag']) {
                $filters['tag'] = $datas['tag'];
            }
            $articles = $this->articleRepository->findBy($filters);
        } else {
            $articles = $this->articleRepository->findAll();
        }

        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
            'filter' => $form->createView(),
        ]);
    }

    #[Route('/new', name: '_new')]
    public function new(Request $request): Response|RedirectResponse
    {
        $article = new Article();

        return $this->handleForm($article, $request);
    }

    #[Route('/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function edit(Article $article, Request $request): Response|RedirectResponse
    {
        return $this->handleForm($article, $request);
    }

    // Permet de passer le state en online
    #[Route('/{id}/state/{state}', name: '_state', requirements: ['id' => '\d+'], methods: 'GET')]
    public function changeState(Article $article, string $state, WorkflowInterface $articlesReservationStateMachine): RedirectResponse
    {
        if ($articlesReservationStateMachine->can($article, $state)) {
            $articlesReservationStateMachine->apply($article, $state);
            $this->addFlash('success', 'Changement de statut effectué.');
        } else {
            $this->addFlash('error', "Erreur lors du changement d'état");
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('admin_article_index');
    }

    private function handleForm(Article $article, Request $request): Response|RedirectResponse
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attachmentFile = $form->get('attachment')->getData();

            if ($attachmentFile) {
                // If a file already exist, delete the file before
                if ($article->getCoverFilename()) {
                    $fs = new Filesystem();
                    $fs->remove($this->uploadFolder.$article->getCoverFilename());
                }

                $originalName = pathinfo($attachmentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = $this->slugger->slug($originalName);
                $newFileName = $safeName.'-'.uniqid('', true).'.'.$attachmentFile->guessExtension();

                $attachmentFile->move($this->uploadFolder, $newFileName);

                $article->setCoverFilename($newFileName);
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'Article ajouté.');

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/article.html.twig', ['form' => $form->createView(), 'article' => $article]);
    }
}
