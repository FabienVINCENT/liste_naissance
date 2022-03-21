<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/admin/user/{id}', name: 'admin_user_toggle_role')]
    public function toggleRoleAdmin(User $user, EntityManagerInterface $em): RedirectResponse
    {
        $roles = $user->getRoles();
        if ($user->getId() !== $this->getUser()->getId()) {
            if (($key = array_search('ROLE_ADMIN', $roles, true)) !== false) {
                unset($roles[$key]);
            } else {
                $roles[] = 'ROLE_ADMIN';
            }
            $user->setRoles($roles);
            $em->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
