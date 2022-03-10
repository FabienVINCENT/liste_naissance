<?php

namespace App\Controller;

use App\Repository\TagRepository;
use App\Repository\TexteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TagRepository $tagRepository, TexteRepository $texteRepository): Response
    {
        $home = $texteRepository->findOneBy(['slot' => 'home']);

        return $this->render('home/index.html.twig', [
            'tags' => $tagRepository->findAll(),
            'home_slot' => $home ? $home->getMessage() : 'slot : home',
        ]);
    }
}
