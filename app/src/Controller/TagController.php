<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/tag', name: 'app_tag_')]
class TagController extends AbstractController
{
    use ControllerTrait;

    public function __construct(private SerializerInterface $serializer, private TagRepository $tagRepository)
    {
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: 'GET')]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/index.html.twig', [
            'tag' => $tag,
            'tags' => $this->tagRepository->findAll(),
        ]);
    }
}
