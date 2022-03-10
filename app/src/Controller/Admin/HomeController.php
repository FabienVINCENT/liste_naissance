<?php

namespace App\Controller\Admin;

use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class HomeController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/home/index.html.twig', [
            'articlesReserved' => $articleRepository->findBy(['status' => 'reserved'], ['reservedAt' => 'DESC']),
        ]);
    }
}
