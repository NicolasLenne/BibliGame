<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur ajouté');
            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'registerForm' => $form->createView(),
        ]);
    }

        /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
      {
         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('main/login.html.twig', [
             'last_username' => $lastUsername,
             'error'         => $error,
          ]);
      }

        /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): void
    {
        // // controller can be blank: it will never be called!
        // return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }
}
