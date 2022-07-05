<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ConsoleRepository;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/myaccount")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="app_account_show", methods={"GET"})
     */
    public function show(ConsoleRepository $consoleRepository, GameRepository $gameRepository): Response
    {
        $nbConsole = count($consoleRepository->findBy(['user' => $this->getUser()]));
        $nbGame = count($gameRepository->findBy(['user' => $this->getUser()]));

        return $this->render('account/show.html.twig', [
            'user' => $this->getUser(),
            'nbConsole' => $nbConsole,
            'nbGame' => $nbGame,
        ]);
    }

    /**
     * @Route("/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete", name="app_account_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
