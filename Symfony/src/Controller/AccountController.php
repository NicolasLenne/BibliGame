<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AccountDeleteType;
use App\Service\PicturesManager;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\ConsoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public function edit(Request $request, UserRepository $userRepository, PicturesManager $picturesManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userPicture = $form->get('picture')->getData();

            if ($userPicture) {
                if(!$picturesManager->add($user, 'picture', $userPicture, 'images_users_directory')){
                    // $this->addFlash('warning', 'Erreur durant le chargement de la photo');
                    return $this->redirectToRoute('app_game_index', [], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_account_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete", name="app_account_delete", methods={"GET", "POST"})
     */
    public function delete(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasherInterface, TokenStorageInterface $tokenStorageInterface, SessionInterface $session, PicturesManager $picturesManager): Response
    {
        $user = $userRepository->find($this->getUser());

        if($request->isMethod('POST')){

            $password = htmlspecialchars($request->request->get('password'));

            $submittedToken = $request->request->get('token');

            if ($this->isCsrfTokenValid('delete', $submittedToken)) {

                $checkPassword = $userPasswordHasherInterface->isPasswordValid($this->getUser(), $password);

                if ($checkPassword && $request->request->get('password') !== null) {
                    if($user->getPicture() !== null){
                        $picturesManager->delete($user, 'picture', 'images_users_directory');
                    }
                    $userRepository->remove($user, true);

                    $tokenStorageInterface->setToken(null);
                    $session->invalidate();

                    return $this->redirectToRoute('main_home', [], Response::HTTP_SEE_OTHER);
                }
                
            }

        }

        return $this->renderForm('account/delete.html.twig', [
            'user' => $user
        ]);
    }
}
